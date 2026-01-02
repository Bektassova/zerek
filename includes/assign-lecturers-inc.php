<?php
session_start();
require_once 'dbh.php';

if (isset($_POST['assign_lecturers'])) {
    $unitId = (int) $_POST['unit_id'];
    $lecturerIds = $_POST['lecturer_ids'] ?? [];

    foreach ($lecturerIds as $lecturerId) {
        // INSERT IGNORE предотвращает дубли
        $sql = "INSERT IGNORE INTO lecturer_units (lecturer_id, unit_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $lecturerId, $unitId);
        mysqli_stmt_execute($stmt);
    }

    $_SESSION['flash_success'] = "Lecturers assigned successfully.";
    header("Location: ../admin-courses.php"); // остаёмся на Course Management
    exit();
}


// fallback
header("Location: ../admin-units.php");
exit();
