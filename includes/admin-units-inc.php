<?php
session_start();
if (isset($_POST["submit"])) {
    require_once 'dbh.php';

    // Получаем данные из формы
    $unitName = $_POST["unit_name"];
    $courseId = $_POST["course_id"];
    $unitDescription = $_POST["unit_description"];
    $createdBy = $_SESSION["userId"]; // ID админа Норы

    // SQL запрос (согласно вашей структуре таблицы units)
    $sql = "INSERT INTO units (unit_name, unit_description, course_id, created_by) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../admin-courses.php?error=stmtfailed");
        exit();
    }

    // "ssii" означает: string, string, integer, integer
    mysqli_stmt_bind_param($stmt, "ssii", $unitName, $unitDescription, $courseId, $createdBy);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Возвращаемся на страницу с успехом
    header("location: ../admin-courses.php?status=success");
    exit();
} else {
    header("location: ../admin-courses.php");
    exit();
}