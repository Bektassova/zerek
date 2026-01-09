<?php
session_start();
require_once "dbh.php";

// Security: only Lecturer
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Lecturer') {
    header("Location: ../login.php");
    exit();
}

$lecturerId = (int) $_SESSION['userId'];

if (!isset($_POST['submission_id'])) {
    header("Location: ../lecturer-submissions.php");
    exit();
}

$submissionId = (int) $_POST['submission_id'];

/*
|--------------------------------------------------------------------------
| Permission check: lecturer can only grade/delete submissions from their units
|--------------------------------------------------------------------------
*/
$sqlCheck = "
SELECT 1
FROM submissions s
JOIN assignments a ON a.assignment_id = s.assignment_id
JOIN lecturer_units lu ON lu.unit_id = a.unit_id
WHERE s.submission_id = ?
  AND lu.lecturer_id = ?
LIMIT 1
";
$stmtCheck = mysqli_prepare($conn, $sqlCheck);
mysqli_stmt_bind_param($stmtCheck, "ii", $submissionId, $lecturerId);
mysqli_stmt_execute($stmtCheck);
$resCheck = mysqli_stmt_get_result($stmtCheck);

if (mysqli_num_rows($resCheck) === 0) {
    $_SESSION['flash_error'] = "Not allowed to modify this submission.";
    header("Location: ../lecturer-submissions.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| DELETE grade
|--------------------------------------------------------------------------
*/
if (isset($_POST['delete_grade'])) {
    $stmtDel = mysqli_prepare($conn, "DELETE FROM grades WHERE submission_id = ?");
    mysqli_stmt_bind_param($stmtDel, "i", $submissionId);
    mysqli_stmt_execute($stmtDel);

    $_SESSION['flash_success'] = "Grade deleted.";
    header("Location: ../lecturer-submissions.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| SAVE grade
|--------------------------------------------------------------------------
*/
if (!isset($_POST['save_grade'])) {
    header("Location: ../lecturer-submissions.php");
    exit();
}

$mark = isset($_POST['mark']) ? (int) $_POST['mark'] : null;
$feedback = isset($_POST['feedback']) ? trim($_POST['feedback']) : "";

// Validate
if ($mark === null || $mark < 0 || $mark > 100) {
    $_SESSION['flash_error'] = "Mark must be between 0 and 100.";
    header("Location: ../lecturer-submissions.php");
    exit();
}
if (strlen($feedback) < 1) {
    $_SESSION['flash_error'] = "Feedback is required.";
    header("Location: ../lecturer-submissions.php");
    exit();
}

// Upsert (works with UNIQUE on grades.submission_id)
$sql = "
INSERT INTO grades (submission_id, lecturer_id, mark, feedback)
VALUES (?, ?, ?, ?)
ON DUPLICATE KEY UPDATE
    lecturer_id = VALUES(lecturer_id),
    mark = VALUES(mark),
    feedback = VALUES(feedback),
    graded_at = CURRENT_TIMESTAMP
";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iiis", $submissionId, $lecturerId, $mark, $feedback);
mysqli_stmt_execute($stmt);

$_SESSION['flash_success'] = "Grade saved.";
header("Location: ../lecturer-submissions.php");
exit();
