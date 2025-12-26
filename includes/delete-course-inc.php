<?php
session_start();
require_once 'dbh.php';

// Security check
if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Admin") {
    header("location: ../login.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("location: ../admin-courses.php?error=noid");
    exit();
}

$courseId = intval($_GET["id"]);

// Extra safety: check if course has units
$sqlCheck = "SELECT COUNT(*) AS cnt FROM units WHERE course_id = ?";
$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sqlCheck);
mysqli_stmt_bind_param($stmt, "i", $courseId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if ($row['cnt'] > 0) {
    // Course has units → cannot delete
    header("location: ../admin-courses.php?error=hasunits");
    exit();
}

// Delete course
$sqlDelete = "DELETE FROM courses WHERE course_id = ?";
$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sqlDelete);
mysqli_stmt_bind_param($stmt, "i", $courseId);
mysqli_stmt_execute($stmt);

header("location: ../admin-courses.php?status=deleted");
exit();
