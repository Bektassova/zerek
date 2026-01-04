<?php
session_start();
require_once 'dbh.php';

// ========================
// CHECK IF FORM IS SUBMITTED
// ========================
if (isset($_POST['assign_lecturers'])) {

    // Get selected unit ID and lecturer IDs
    $unitId = isset($_POST['unit_id']) ? (int)$_POST['unit_id'] : 0;
    $lecturerIds = $_POST['lecturer_ids'] ?? [];

    // Basic validation: must select unit and at least one lecturer
    if ($unitId === 0 || empty($lecturerIds)) {
        $_SESSION['flash_error'] = "Please select a unit and at least one lecturer.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // ========================
    // PREPARE SQL: INSERT LECTURER INTO UNIT
    // ========================
    // INSERT IGNORE prevents duplicate entries
    $sql = "INSERT IGNORE INTO lecturer_units (lecturer_id, unit_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        $_SESSION['flash_error'] = "Database error: could not prepare statement.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // ========================
    // LOOP THROUGH SELECTED LECTURERS AND INSERT
    // ========================
    $assignedCount = 0; // Counter for successful assignments
    foreach ($lecturerIds as $lecturerId) {
        $lecturerId = (int)$lecturerId;

        mysqli_stmt_bind_param($stmt, "ii", $lecturerId, $unitId);
        if (mysqli_stmt_execute($stmt)) {
            $assignedCount++;
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);

    // ========================
    // SET FLASH MESSAGE BASED ON RESULT
    // ========================
    if ($assignedCount > 0) {
        $_SESSION['flash_success'] = "Successfully assigned $assignedCount lecturer(s) to the unit.";
    } else {
        $_SESSION['flash_info'] = "No new lecturers were assigned (they may already be assigned).";
    }

    // Redirect back to the page where the form was submitted
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// ========================
// FALLBACK: IF SOMEONE ACCESSES THIS SCRIPT DIRECTLY
// ========================
$_SESSION['flash_error'] = "Invalid request.";
header("Location: ../admin-units.php");
exit();
?>
