<?php
session_start();
require_once 'dbh.php';

if (isset($_POST['create_assignment'])) {
    $unitId = $_POST['unit_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $dueDate = $_POST['due_date'];
    $lecturerId = $_SESSION['userId']; // Alice's Passport ID (25)

    // Handle File Upload
    $fileName = $_FILES['assignment_file']['name'];
    $fileTmpName = $_FILES['assignment_file']['tmp_name'];
    $fileDestination = "";

    if (!empty($fileName)) {
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = array('pdf', 'doc', 'docx');

        if (in_array($fileExt, $allowed)) {
            $newFileName = uniqid('', true) . "." . $fileExt;
            $fileDestination = '../uploads/' . $newFileName;
            move_uploaded_file($fileTmpName, $fileDestination);
        }
    }

    /**
     * INSERT into assignments table
     * We use the lecturer's user_id (25) as the creator of this assignment
     */
    $sql = "INSERT INTO assignments (unit_id, lecturer_id, title, description, due_date, file_path) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iissss", $unitId, $lecturerId, $title, $description, $dueDate, $newFileName);

    if (mysqli_stmt_execute($stmt)) {
        header("location: ../lecturer-assignments.php?upload=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    header("location: ../create-assignment.php");
}