<?php
session_start();
require_once 'dbh.php';

// Security check
if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Admin") {
    header("location: ../login.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("location: ../admin-units.php?error=noid");
    exit();
}

$unitId = intval($_GET["id"]);
$returnPage = $_GET['return'] ?? 'admin-units.php';

// Optional: check if unit has enrolled students or lecturers
// (можно добавить позже)

// Delete unit
$sqlDelete = "DELETE FROM units WHERE unit_id = ?";
$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sqlDelete);
mysqli_stmt_bind_param($stmt, "i", $unitId);
mysqli_stmt_execute($stmt);

$_SESSION['flash_success'] = "Unit deleted successfully.";
header("location: ../" . $returnPage);
exit();
