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
$returnPage = $_GET['return'] ?? 'admin-courses.php';

// Check if course has units
$sqlCheck = "SELECT COUNT(*) AS cnt FROM units WHERE course_id = ?";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sqlCheck)) {
    $_SESSION['flash_error'] = "Failed to prepare SQL statement.";
    header("location: ../" . $returnPage);
    exit();
}

mysqli_stmt_bind_param($stmt, "i", $courseId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if ($row['cnt'] > 0) {
    $_SESSION['flash_error'] = "Cannot delete course: it has units.";
    header("location: ../" . $returnPage);
    exit();
}

// Delete course
$sqlDelete = "DELETE FROM courses WHERE course_id = ?";
$stmtDelete = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmtDelete, $sqlDelete)) {
    $_SESSION['flash_error'] = "Failed to prepare delete statement.";
    header("location: ../" . $returnPage);
    exit();
}

mysqli_stmt_bind_param($stmtDelete, "i", $courseId);
if (mysqli_stmt_execute($stmtDelete)) {
    $_SESSION['flash_success'] = "Course deleted successfully.";
} else {
    $_SESSION['flash_error'] = "Failed to delete course.";
}

mysqli_stmt_close($stmtDelete);
mysqli_close($conn);

header("location: ../" . $returnPage);
exit();
