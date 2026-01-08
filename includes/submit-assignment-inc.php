<?php
session_start();
require_once "dbh.php";

/*
|--------------------------------------------------------------------------
| 1. Security check
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_POST['assignment_id'])) {
    header("Location: ../student-assignments.php");
    exit();
}

$studentId    = $_SESSION['userId'];
$assignmentId = (int) $_POST['assignment_id'];

// $_FILES['files']

$uploadDir = "../uploads/submissions/";

foreach ($_FILES['files']['tmp_name'] as $index => $tmpName) {

    if ($_FILES['files']['error'][$index] === 0) {

        $fileName = time() . "_" . basename($_FILES['files']['name'][$index]);
        $targetPath = $uploadDir . $fileName;

        move_uploaded_file($tmpName, $targetPath);

        
    }
}


/*
|--------------------------------------------------------------------------
| 2. Create submission record
|--------------------------------------------------------------------------
*/
$sql = "INSERT INTO submissions (assignment_id, student_id)
        VALUES (?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $assignmentId, $studentId);
mysqli_stmt_execute($stmt);

$submissionId = mysqli_insert_id($conn);

/*
|--------------------------------------------------------------------------
| 3. Handle file uploads (MULTIPLE files supported)
|--------------------------------------------------------------------------
*/
if (!empty($_FILES['files']['name'][0])) {

    $uploadDir = "../uploads/submissions/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    foreach ($_FILES['files']['tmp_name'] as $index => $tmpName) {

        if ($_FILES['files']['error'][$index] === UPLOAD_ERR_OK) {

            $originalName = basename($_FILES['files']['name'][$index]);
            $fileName = time() . "_" . $originalName;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($tmpName, $targetPath)) {

                $sqlFile = "INSERT INTO submission_files (submission_id, file_path)
                            VALUES (?, ?)";
                $stmtFile = mysqli_prepare($conn, $sqlFile);
                mysqli_stmt_bind_param($stmtFile, "is", $submissionId, $fileName);
                mysqli_stmt_execute($stmtFile);
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| 4. Flash message + redirect
|--------------------------------------------------------------------------
*/
$_SESSION['flash_success'] = "Assignment submitted successfully.";
header("Location: ../student-assignments.php");
exit();
