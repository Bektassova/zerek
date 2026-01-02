<?php
session_start();
require_once "dbh.php";

if (!isset($_POST['enroll_students'])) {
    // Если форма не была отправлена, просто вернемся
    header("Location: ../admin-units.php");
    exit();
}

$unitId = (int) $_POST['unit_id'];
$studentIds = $_POST['student_ids'] ?? [];

if (empty($studentIds)) {
    $_SESSION['flash_success'] = "No students selected.";
    header("Location: ../admin-unit-enroll.php?unit_id=$unitId");
    exit();
}

// Добавляем выбранных студентов в таблицу student_units
foreach ($studentIds as $studentId) {
    $sql = "INSERT IGNORE INTO student_units (student_id, unit_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $studentId, $unitId);
    mysqli_stmt_execute($stmt);
}

// Устанавливаем сообщение о успехе
$_SESSION['flash_success'] = "Students successfully enrolled to this unit.";

// Перенаправляем обратно на страницу Enroll Students
header("Location: ../admin-unit-enroll.php?unit_id=$unitId");
exit();

