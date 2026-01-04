<?php
session_start();

require_once 'dbh.php';

if (isset($_POST['assign_lecturers'])) {
    // 1. Проверяем, есть ли вообще соединение с базой
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $unitId = (int)$_POST['unit_id'];
    $lecturerIds = $_POST['lecturer_ids'] ?? [];

    // 2. Готовим запрос БЕЗ слова IGNORE, чтобы поймать ошибку
    $sql = "INSERT INTO lecturer_units (lecturer_id, unit_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("SQL Prepare Error: " . mysqli_error($conn));
    }

    $assignedCount = 0;
    foreach ($lecturerIds as $lecturerId) {
        $lId = (int)$lecturerId;
        mysqli_stmt_bind_param($stmt, "ii", $lId, $unitId);
        
        if (mysqli_stmt_execute($stmt)) {
            $assignedCount += mysqli_stmt_affected_rows($stmt);
        } else {
            // 3. Если вставка не удалась — выводим ПОЛНУЮ ошибку базы
            echo "<h3>Database Error Details:</h3>";
            echo "Attempted to link Lecturer ID: $lId with Unit ID: $unitId <br>";
            echo "Error message: " . mysqli_error($conn) . "<br>";
            echo "Error code: " . mysqli_errno($conn);
            die(); 
        }
    }

    mysqli_stmt_close($stmt);

    if ($assignedCount > 0) {
        $_SESSION['flash_success'] = "Successfully assigned $assignedCount lecturer(s).";
    } else {
        $_SESSION['flash_info'] = "Nothing was added.";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}