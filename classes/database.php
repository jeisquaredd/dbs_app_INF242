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