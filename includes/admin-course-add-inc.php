<?php
session_start();
if (isset($_POST["submit_course"])) {
    require_once 'dbh.php';
    $courseName = $_POST["course_name"];

    $sql = "INSERT INTO courses (course_name) VALUES (?);";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $courseName);
        mysqli_stmt_execute($stmt);
        header("location: ../admin-courses.php?status=courseadded");
    } else {
        header("location: ../admin-courses.php?error=failed");
    }
    exit();
}