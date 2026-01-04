<?php
session_start();
require_once 'dbh.php';

// Security check
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Lecturer') {
    header("Location: ../login.php");
    exit();
}

$lecturerId = $_SESSION['userId'];

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM assignments WHERE assignment_id = ? AND lecturer_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $id, $lecturerId);
    mysqli_stmt_execute($stmt);

    $_SESSION['flash_success'] = "Assignment deleted successfully.";
    header("Location: ../assignments.php");
    exit();
}

// CREATE or UPDATE
if (isset($_POST['create_assignment'])) {
    $unit_id = intval($_POST['unit_id']);
    $title = $_POST['title'];
    $description = $_POST['description'] ?? '';
    $due_date = $_POST['due_date'] ?? null;

    // File upload
    $file_path = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $filename = time() . '_' . basename($_FILES['file']['name']);
        $target = '../uploads/assignments/' . $filename;
        move_uploaded_file($_FILES['file']['tmp_name'], $target);
        $file_path = 'uploads/assignments/' . $filename;
    }

    if (!empty($_POST['assignment_id'])) {
        // UPDATE
        $assignment_id = intval($_POST['assignment_id']);
        $sql = "UPDATE assignments 
                SET unit_id=?, title=?, description=?, file_path=?, due_date=? 
                WHERE assignment_id=? AND lecturer_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "issssii", $unit_id, $title, $description, $file_path, $due_date, $assignment_id, $lecturerId);
        mysqli_stmt_execute($stmt);
        $_SESSION['flash_success'] = "Assignment updated successfully.";
    } else {
        // INSERT
        $sql = "INSERT INTO assignments (unit_id, lecturer_id, title, description, file_path, due_date) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iissss", $unit_id, $lecturerId, $title, $description, $file_path, $due_date);
        mysqli_stmt_execute($stmt);
        $_SESSION['flash_success'] = "Assignment created successfully.";
    }

    header("Location: ../assignments.php");
    exit();
}
