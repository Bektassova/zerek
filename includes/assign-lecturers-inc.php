<?php
session_start();
require_once 'dbh.php';

if (isset($_POST['assign_lecturers'])) {

    $unitId = (int) $_POST['unit_id'];
    $lecturerIds = $_POST['lecturer_ids'] ?? [];

    if (empty($unitId) || empty($lecturerIds)) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $sql = "INSERT IGNORE INTO lecturer_units (lecturer_id, unit_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    foreach ($lecturerIds as $lecturerId) {
        mysqli_stmt_bind_param($stmt, "ii", $lecturerId, $unitId);
        mysqli_stmt_execute($stmt);
    }

    $_SESSION['flash_success'] = "Lecturer assigned successfully.";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// fallback
header("Location: ../admin-units.php");
exit();
