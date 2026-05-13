<?php

class database{

function opencon(): PDO{
    return new PDO("mysql:host=localhost;
    dbname=INF242_LMS",
    username: "root",
    password: "");
}

function insertUser($email, $password_hash, $is_active, $member_since){
    $con = $this->opencon();

    try{
        $con->beginTransaction();
        $stmt = $con->prepare("INSERT INTO Users (username, password_hash, is_active) VALUES (?, ?,?)");
        $stmt->execute([$email, $password_hash, $is_active,]);
        $user_id = $con->lastInsertId(); // Get the new user_id for mapping
        $con->commit();
        return $user_id; // Return the new user_id
    
        }catch (PDOException $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        throw $e; // Re-throw the exception after rolling back              
}

}

function insertBorrower($firstname, $lastname, $email, $phone, $is_active, $member_since){
    $con = $this->opencon();

    try{
        $con->beginTransaction();
        $stmt = $con->prepare("INSERT INTO Borrowers (borrower_firstname, borrower_lastname, borrower_email, borrower_phone_number, borrower_member_since, is_active) VALUES (?, ?,?, ?, ?, ?)");
        $stmt->execute([$firstname, $lastname, $email, $phone, $member_since, $is_active]);
        $borrower_id = $con->lastInsertId(); // Get the new borrower_id for mapping
        $con->commit();
        return $borrower_id; // Return the new borrower_id
    
        }catch (PDOException $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        throw $e; // Re-throw the exception after rolling back              
}

}

function insertBorrowerUser($borrower_id, $user_id){
    $con = $this->opencon();

    try{
        $con->beginTransaction();
        $stmt = $con->prepare("INSERT INTO BorrowerUser (borrower_id, user_id) VALUES (?, ?)");
        $stmt->execute([$borrower_id, $user_id]);
        $con->commit();
        return true; // Successfully inserted mapping
    
        }catch (PDOException $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        throw $e; // Re-throw the exception after rolling back              

}

}
function viewusers()
        {
            $con = $this->opencon();
            return $con->query("SELECT * FROM Borrowers")->fetchAll();
        }


function insertBorrowerAddress($borrower_id, $house_number, $street, $barangay, $city, $province, $postal_code, $is_primary)
{
    $con = $this->opencon();

    try {
        $con->beginTransaction();
        $stmt = $con->prepare("INSERT INTO BorrowerAddress (borrower_id, ba_house_number, ba_street, ba_barangay, ba_city, ba_province, ba_postal_code, is_primary) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$borrower_id, $house_number, $street, $barangay, $city, $province, $postal_code, $is_primary]);
        $con->commit();
        return true; // Successfully inserted address
    } catch (PDOException $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        throw $e; // Re-throw the exception after rolling back
    }
}

function viewbooks()
        {
            $con = $this->opencon();
            return $con->query("SELECT
            Books.book_id,
            Books.book_title,
            Books.book_isbn,
            Books.book_publication_year,
            Books.book_publisher,
            COUNT(BookCopy.copy_id) AS Copies,
            SUM(BookCopy.status = 'Available') AS Available_Copies
            FROM
            Books
            JOIN BookCopy ON Books.book_id = BookCopy.book_id
            GROUP BY 1")->fetchAll();
        }


function viewloans()
        {
            $con = $this->opencon();
            return $con->query("SELECT
            Loan.loan_id,
            CONCAT(Borrowers.borrower_firstname,' ',Borrowers.borrower_lastname) AS Borrower,
            Loan.loan_status,
            DATE(loan.Loan_date) AS loan_date,
            Users.username
            FROM
            Loan
            JOIN Borrowers ON Loan.borrower_id = Borrowers.borrower_id
            JOIN Users ON Loan.processed_by_user_id = Users.user_id
            ORDER BY loan.loan_status, loan.loan_date DESC
            ")->fetchAll();
        }

function createLoanWithItems($borrower_id, $processed_by_user_id, array $copy_ids, $due_date, $condition_out)
{
    $con = $this->opencon();
    $copy_ids = array_values(array_unique(array_filter(array_map('intval', $copy_ids), function ($id) {
        return $id > 0;
    })));

    if (empty($copy_ids)) {
        throw new InvalidArgumentException('At least one copy ID is required.');
    }

    try {
        $con->beginTransaction();

        $placeholders = implode(',', array_fill(0, count($copy_ids), '?'));

        $borrowerStmt = $con->prepare("SELECT 1 FROM Borrowers WHERE borrower_id = ?");
        $borrowerStmt->execute([$borrower_id]);
        if (!$borrowerStmt->fetchColumn()) {
            throw new RuntimeException('Borrower not found.');
        }

        $processorStmt = $con->prepare("SELECT 1 FROM Users WHERE user_id = ?");
        $processorStmt->execute([$processed_by_user_id]);
        if (!$processorStmt->fetchColumn()) {
            throw new RuntimeException('Processor user not found.');
        }

        $availabilitySql = "
            SELECT bc.copy_id
            FROM BookCopy bc
            WHERE bc.copy_id IN ($placeholders)
              AND UPPER(bc.status) = 'AVAILABLE'
              AND NOT EXISTS (
                SELECT 1
                FROM LoanItem li
                JOIN Loan l ON l.loan_id = li.loan_id
                WHERE li.copy_id = bc.copy_id
                  AND l.loan_status = 'OPEN'
                  AND li.li_returned_at IS NULL
              )
        ";

        $availabilityStmt = $con->prepare($availabilitySql);
        $availabilityStmt->execute($copy_ids);
        $availableIds = array_map('intval', $availabilityStmt->fetchAll(PDO::FETCH_COLUMN));

        $missingIds = array_diff($copy_ids, $availableIds);
        if (!empty($missingIds)) {
            throw new RuntimeException('Copies not available or already on loan: ' . implode(', ', $missingIds));
        }

        $loanStmt = $con->prepare("INSERT INTO Loan (borrower_id, processed_by_user_id, loan_status, loan_date) VALUES (?, ?, 'OPEN', NOW())");
        $loanStmt->execute([$borrower_id, $processed_by_user_id]);
        $loan_id = $con->lastInsertId();

        $loanItemStmt = $con->prepare("INSERT INTO LoanItem (loan_id, copy_id, li_duedate, condition_out) VALUES (?, ?, ?, ?)");
        foreach ($copy_ids as $copy_id) {
            $loanItemStmt->execute([$loan_id, $copy_id, $due_date, $condition_out]);
        }

        $updateStmt = $con->prepare("UPDATE BookCopy SET status = 'ON_LOAN' WHERE copy_id IN ($placeholders)");
        $updateStmt->execute($copy_ids);

        $con->commit();
        return $loan_id;
    } catch (Exception $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        throw $e;
    }
}

function insertAuthor($firstname, $lastname, $birth_year, $nationality){
    $con = $this->opencon();

    try {
        $con->beginTransaction();
        $stmt = $con->prepare("INSERT INTO Authors (author_firstname, author_lastname, author_birth_year, author_nationality) VALUES (?, ?, ?, ?)");
        $stmt->execute([$firstname, $lastname, $birth_year, $nationality]);
        $author_id = $con->lastInsertId();
        $con->commit();
        return $author_id;
    } catch (PDOException $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        throw $e;
    }
}

function viewauthors()
        {
            $con = $this->opencon();
            return $con->query("SELECT * FROM Authors ORDER BY author_lastname, author_firstname")->fetchAll();
        }

function insertGenre($genre_name){
    $con = $this->opencon();

    try {
        $con->beginTransaction();
        $stmt = $con->prepare("INSERT INTO Genres (genre_name) VALUES (?)");
        $stmt->execute([$genre_name]);
        $genre_id = $con->lastInsertId();
        $con->commit();
        return $genre_id;
    } catch (PDOException $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        throw $e;
    }
}

function viewgenres()
        {
            $con = $this->opencon();
            return $con->query("SELECT * FROM Genres ORDER BY genre_name")->fetchAll();
        }

function updateBook($book_id, $title, $isbn, $year, $publisher)
{
    $con = $this->opencon();

    try {
        $con->beginTransaction();

        $stmt = $con->prepare("
            UPDATE Books
            SET book_title = ?, 
                book_isbn = ?, 
                book_publication_year = ?, 
                book_publisher = ?
            WHERE book_id = ?
        ");

        $stmt->execute([$title, $isbn, $year, $publisher, $book_id]);

        $con->commit();
        return true; // Successfully updated

    } catch (PDOException $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        throw $e;
    }
}

function countBook(){
    $con = $this->opencon();
    return $con->query("SELECT COUNT(*) AS total_books FROM Books")->fetchColumn();
}

function countAvailBook(){
    $con = $this->opencon();
    return $con->query("SELECT SUM(status = 'Available') as Total_Books from BookCopy")->fetchColumn();
}//invoke 

function deletebooks($book_id){
    $con = $this->opencon();

    try {
        $con->beginTransaction();

        // First, delete all copies of the book
        $stmtCopies = $con->prepare("DELETE FROM BookCopy WHERE book_id = ?");
        $stmtCopies->execute([$book_id]);

        // Then, delete the book itself
        $stmtBook = $con->prepare("DELETE FROM Books WHERE book_id = ?");
        $stmtBook->execute([$book_id]);

        $con->commit();
        return true; // Successfully deleted

    } catch (PDOException $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        throw $e;
    }
}

}
