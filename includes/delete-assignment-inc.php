<?php
session_start();
require_once 'dbh.php';

// Security check: only the assigned lecturer can delete
if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Lecturer") {
    header("location: ../login.php");
    exit();
}

if (isset($_GET["id"])) {
    $assignmentId = intval($_GET["id"]);
    $lecturerId = $_SESSION["userId"];

    // First, we check if this assignment belongs to the logged-in lecturer
    $sql = "DELETE FROM assignments WHERE assignment_id = ? AND lecturer_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $assignmentId, $lecturerId);

    if (mysqli_stmt_execute($stmt)) {
        header("location: ../lecturer-assignments.php?delete=success");
    } else {
        header("location: ../lecturer-assignments.php?error=deletefailed");
    }
} else {
    header("location: ../lecturer-assignments.php");
}
exit();