<?php
require_once 'dbh.php';

if (isset($_POST["submit_enroll"])) {

    $userId = $_POST["user_id"];
    $courseId = $_POST["course_id"];

    // 🔐 ЗАЩИТА
    if (empty($courseId)) {
        header("location: ../admin-users.php?error=emptycourse");
        exit();
    }

    $sql = "UPDATE users SET course_id = ? WHERE user_id = ?";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $courseId, $userId);
        mysqli_stmt_execute($stmt);
        
session_start();

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['flash_success'] = "Student successfully assigned to course.";
    header("location: ../admin-users.php");
    exit();
} else {
    $_SESSION['flash_error'] = "Error assigning student.";
    header("location: ../admin-users.php");
    exit();
}

    }

} else {
    header("location: ../admin-users.php");
    exit();
}
?>