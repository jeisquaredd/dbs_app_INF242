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



}