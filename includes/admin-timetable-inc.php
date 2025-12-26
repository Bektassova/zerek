<?php
session_start();
if (isset($_POST["submit"])) {
    require_once 'dbh.php';

    $studentId = $_POST["student_id"];
    $subject = $_POST["subject"];
    $day = $_POST["day"];
    $time = $_POST["time"];
    $room = $_POST["room"];

    $sql = "INSERT INTO timetable (subject_name, class_day, start_time, room_number, user_id) VALUES (?, ?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../admin-timetable.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ssssi", $subject, $day, $time, $room, $studentId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../admin-timetable.php?status=success");
    exit();
} else {
    header("location: ../admin-timetable.php");
    exit();
}