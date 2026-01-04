<?php
session_start();
require_once "dbh.php";

if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Lecturer") {
    header("location: ../login.php");
    exit();
}

if (!isset($_POST['update_assignment'])) {
    header("location: ../lecturer-assignments.php");
    exit();
}

$assignmentId = (int)$_POST['assignment_id'];
$unitId = (int)$_POST['unit_id'];
$title = $_POST['title'];
$description = $_POST['description'];
$dueDate = $_POST['due_date'];

$lecturerUserId = $_SESSION["userId"];

// Get lecturer_id
$sqlLecturer = "SELECT lecturer_id FROM lecturers WHERE user_id=?";
$stmt = mysqli_prepare($conn, $sqlLecturer);
mysqli_stmt_bind_param($stmt, "i", $lecturerUserId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$lecturer = mysqli_fetch_assoc($result);

if (!$lecturer) {
    die("Lecturer record not found.");
}
$lecturerId = $lecturer['lecturer_id'];

// Optional file upload
$filePath = null;
if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['assignment_file']['tmp_name'];
    $fileName = basename($_FILES['assignment_file']['name']);
    $targetDir = "../uploads/assignments/";
    if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
    $filePath = $targetDir . time() . "_" . $fileName;
    move_uploaded_file($fileTmp, $filePath);
}

// Update assignment
$sqlUpdate = "UPDATE assignments SET unit_id=?, title=?, description=?, due_date=?";
if ($filePath) {
    $sqlUpdate .= ", file_path=?";
}
$sqlUpdate .= " WHERE assignment_id=? AND unit_id IN (SELECT unit_id FROM lecturer_units WHERE lecturer_id=?)";

$stmt = mysqli_prepare($conn, $sqlUpdate);

if ($filePath) {
    mysqli_stmt_bind_param($stmt, "isssisi", $unitId, $title, $description, $dueDate, $filePath, $assignmentId, $lecturerId);
} else {
    mysqli_stmt_bind_param($stmt, "isssii", $unitId, $title, $description, $dueDate, $assignmentId, $lecturerId);
}

mysqli_stmt_execute($stmt);

header("location: ../lecturer-assignments.php?update=success");
exit();
?>
