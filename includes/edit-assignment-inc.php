<?php
session_start();
require_once "dbh.php";

// SECURITY: Only allow Lecturers to update assignments
if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Lecturer") {
    header("location: ../login.php");
    exit();
}

if (isset($_POST['update_assignment'])) {
    $assignmentId = (int)$_POST['assignment_id'];
    $unitId = (int)$_POST['unit_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $dueDate = $_POST['due_date'];
    $lecturerUserId = $_SESSION["userId"]; // Uses Passport ID (e.g., 25)

    // Check for new file upload
    $filePath = null;
    if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['assignment_file']['tmp_name'];
        $fileName = basename($_FILES['assignment_file']['name']);
        $targetDir = "../uploads/"; 
        
        // Create directory if it doesn't exist
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        
        $dbFileName = time() . "_" . $fileName;
        if (move_uploaded_file($fileTmp, $targetDir . $dbFileName)) {
            $filePath = $dbFileName;
        }
    }

    /**
     * UPDATE Logic:
     * We update the assignment only if it belongs to the logged-in lecturer.
     */
    if ($filePath) {
        $sql = "UPDATE assignments SET unit_id=?, title=?, description=?, due_date=?, file_path=? 
                WHERE assignment_id=? AND lecturer_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "issssii", $unitId, $title, $description, $dueDate, $filePath, $assignmentId, $lecturerUserId);
    } else {
        $sql = "UPDATE assignments SET unit_id=?, title=?, description=?, due_date=? 
                WHERE assignment_id=? AND lecturer_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isssii", $unitId, $title, $description, $dueDate, $assignmentId, $lecturerUserId);
    }

    mysqli_stmt_execute($stmt);
    header("location: ../lecturer-assignments.php?update=success");
    exit();
} else {
    header("location: ../lecturer-assignments.php");
    exit();
}