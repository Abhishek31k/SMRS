<?php
session_start();
include('includes/config.php');

// Check if student is logged in
if(!isset($_SESSION['StudentId'])){
    echo "<div class='alert alert-danger'>You must be logged in.</div>";
    exit;
}

$StudentId = $_SESSION['StudentId'];

// ----------------- DELETE FEE -----------------
if(isset($_POST['delete_fee'], $_POST['feeId'])){
    $sql = "DELETE FROM tblfees WHERE id=:id AND StudentId=:sid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $_POST['feeId'], PDO::PARAM_INT);
    $query->bindParam(':sid', $StudentId, PDO::PARAM_INT);
    if($query->execute()){
        echo "<div class='alert alert-success'>Fee record deleted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to delete fee record.</div>";
    }
    exit;
}

// ----------------- ADD / UPDATE FEE -----------------
if(isset($_POST['semester'], $_POST['universityFee'], $_POST['collegeFee'], $_POST['busFee'])){

    $semester = trim($_POST['semester']);
    $univFee = trim($_POST['universityFee']);
    $collegeFee = trim($_POST['collegeFee']);
    $busFee = trim($_POST['busFee']);

    if(!empty($_POST['feeId'])) {
        // Update existing fee
        $sql = "UPDATE tblfees 
                SET Semester=:semester, UniversityFee=:univ, CollegeFee=:college, BusFee=:bus 
                WHERE id=:id AND StudentId=:sid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $_POST['feeId'], PDO::PARAM_INT);
        $msg = "Fee record updated successfully.";
    } else {
        // Insert new fee
        $sql = "INSERT INTO tblfees (StudentId, Semester, UniversityFee, CollegeFee, BusFee) 
                VALUES (:sid, :semester, :univ, :college, :bus)";
        $query = $dbh->prepare($sql);
        $msg = "Fee record added successfully.";
    }

    // Bind parameters
    $query->bindParam(':sid', $StudentId, PDO::PARAM_INT);
    $query->bindParam(':semester', $semester, PDO::PARAM_STR);
    $query->bindParam(':univ', $univFee, PDO::PARAM_INT);
    $query->bindParam(':college', $collegeFee, PDO::PARAM_INT);
    $query->bindParam(':bus', $busFee, PDO::PARAM_INT);

    if($query->execute()){
        echo "<div class='alert alert-success'>{$msg}</div>";
    } else {
        echo "<div class='alert alert-danger'>Error saving fee record.</div>";
    }
    exit;
}
?>
