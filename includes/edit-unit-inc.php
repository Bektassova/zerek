<?php
session_start();
require_once 'dbh.php';

// Security check
if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Admin") {
    header("location: ../login.php");
    exit();
}

// Check required POST data
if (!isset($_POST["unit_id"], $_POST["unit_name"])) {
    header("location: ../admin-courses.php");
    exit();
}

$unitId = (int) $_POST["unit_id"];
$unitName = $_POST["unit_name"];
$unitDescription = $_POST["unit_description"] ?? null;

// Update unit
$sql = "UPDATE units SET unit_name = ?, unit_description = ? WHERE unit_id = ?";
$stmt = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt, $sql)) {
    die("SQL error");
}

mysqli_stmt_bind_param($stmt, "ssi", $unitName, $unitDescription, $unitId);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Redirect back
header("location: ../admin-courses.php?status=unitupdated");
exit();
