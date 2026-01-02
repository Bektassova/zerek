<?php
session_start();
require_once 'dbh.php';

// Allow delete only for Admin and only if unit_id is provided
if (isset($_GET['id']) && $_SESSION['role'] === 'Admin') {

    $unitId = $_GET['id'];

    // Delete unit by ID (safe & correct)
    $sql = "DELETE FROM units WHERE unit_id = ?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../admin-courses.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "i", $unitId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Flash message
    $_SESSION['flash_success'] = "Unit deleted successfully.";
    header("Location: ../admin-units.php");
    exit();

    // Redirect back after delete
    header("Location: ../admin-courses.php?status=unitdeleted");
    exit();
}

// If something is wrong — go back
header("Location: ../admin-courses.php");
exit();
