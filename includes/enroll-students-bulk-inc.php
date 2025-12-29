<?php
session_start();
require_once 'dbh.php';

if (!isset($_POST['bulk_assign'])) {
    header("Location: ../admin-users.php");
    exit();
}

if (
    empty($_POST['student_ids']) ||
    empty($_POST['course_id'])
) {
    $_SESSION['flash_error'] = "Please select students and a course.";
    header("Location: ../admin-users.php");
    exit();
}

$studentIds = $_POST['student_ids']; // массив
$courseId = (int) $_POST['course_id'];

$sql = "UPDATE users SET course_id = ? WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    $_SESSION['flash_error'] = "Database error.";
    header("Location: ../admin-users.php");
    exit();
}

foreach ($studentIds as $studentId) {
    mysqli_stmt_bind_param($stmt, "ii", $courseId, $studentId);
    mysqli_stmt_execute($stmt);
}

$_SESSION['flash_success'] = "Students successfully assigned to the course.";
header("Location: ../admin-users.php");
exit();
