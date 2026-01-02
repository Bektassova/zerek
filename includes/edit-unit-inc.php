<?php
session_start();
require_once 'dbh.php';

// Security check
if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Admin") {
    header("location: ../login.php");
    exit();
}

// Required data check
if (!isset($_POST["unit_id"], $_POST["unit_name"])) {
    header("location: ../admin-units.php");
    exit();
}

$unitId      = (int) $_POST['unit_id'];
$unitName    = trim($_POST['unit_name']);
$description = trim($_POST['unit_description'] ?? '');

// Update ONLY what exists in the form
$sql = "UPDATE units 
        SET unit_name = ?, unit_description = ?
        WHERE unit_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssi", $unitName, $description, $unitId);
mysqli_stmt_execute($stmt);

// Flash message
$_SESSION['flash_success'] = "Unit updated successfully.";

// Redirect to Unit Management
header("Location: ../admin-units.php");
exit();
?>