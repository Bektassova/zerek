<?php
session_start();
require_once "dbh.php";

// Admin only
if (!isset($_SESSION["userId"]) || ($_SESSION["role"] ?? "") !== "Admin") {
    header("Location: ../login.php");
    exit();
}

if (!isset($_POST["add_timetable"])) {
    header("Location: ../admin-timetable.php");
    exit();
}

// Read inputs
$courseId     = (int)($_POST["course_id"] ?? 0);
$classDay     = trim($_POST["class_day"] ?? "");
$startTime    = trim($_POST["start_time"] ?? "");
$endTime      = trim($_POST["end_time"] ?? "");
$subjectName  = trim($_POST["subject_name"] ?? "");
$roomNumber   = trim($_POST["room_number"] ?? "");
$classType    = trim($_POST["class_type"] ?? "");

// Validation
if (
    $courseId <= 0 ||
    $classDay === "" ||
    $startTime === "" ||
    $endTime === "" ||
    $subjectName === "" ||
    $roomNumber === "" ||
    $classType === ""
) {
    $_SESSION["flash_error"] = "Please fill in all fields.";
    header("Location: ../admin-timetable.php");
    exit();
}

/*
IMPORTANT:
Your timetable table must have:
id, subject_name, class_day, start_time, end_time,
room_number, class_type, user_id, course_id
*/

$sql = "INSERT INTO timetable 
        (course_id, user_id, subject_name, class_day, start_time, end_time, room_number, class_type)
        VALUES (?, NULL, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "issssss",
    $courseId,
    $subjectName,
    $classDay,
    $startTime,
    $endTime,
    $roomNumber,
    $classType
);

mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$_SESSION["flash_success"] = "Timetable entry added successfully.";
header("Location: ../admin-timetable.php?course_id=" . $courseId);
exit();
