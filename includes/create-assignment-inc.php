<?php
session_start();
require_once "dbh.php";

/*
|--------------------------------------------------------------------------
| 1) Security
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Lecturer") {
    header("Location: ../login.php");
    exit();
}

if (!isset($_POST["create_assignment"])) {
    header("Location: ../create-assignment.php");
    exit();
}

// The logged-in lecturer user ID (from users table)
$lecturerUserId = (int) $_SESSION["userId"];

/*
|--------------------------------------------------------------------------
| 2) Read form data
|--------------------------------------------------------------------------
*/
$unitId      = (int) ($_POST["unit_id"] ?? 0);
$title       = trim($_POST["title"] ?? "");
$description = trim($_POST["description"] ?? "");
$dueDate     = !empty($_POST["due_date"]) ? $_POST["due_date"] : null;

if ($unitId <= 0 || $title === "") {
    $_SESSION["flash_error"] = "Unit and title are required.";
    header("Location: ../create-assignment.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| 3) Handle file upload (Assignment File)
|--------------------------------------------------------------------------
| IMPORTANT:
| - Save to uploads/assignments/
| - Store DB path as: uploads/assignments/filename.ext
|--------------------------------------------------------------------------
*/
$filePathForDb = null;

if (isset($_FILES["assignment_file"]) && $_FILES["assignment_file"]["error"] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES["assignment_file"]["error"] === UPLOAD_ERR_OK) {

        $uploadDirFs = __DIR__ . "/../uploads/assignments/"; // filesystem path
        $uploadDirDb = "uploads/assignments/";               // db path

        if (!is_dir($uploadDirFs)) {
            mkdir($uploadDirFs, 0777, true);
        }

        $originalName = basename($_FILES["assignment_file"]["name"]);
        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);

        $ext = strtolower(pathinfo($safeName, PATHINFO_EXTENSION));
        $allowed = ["pdf", "doc", "docx"];

        if (!in_array($ext, $allowed, true)) {
            $_SESSION["flash_error"] = "Invalid file type. Allowed: PDF, DOC, DOCX.";
            header("Location: ../create-assignment.php");
            exit();
        }

        $fileName = time() . "_" . $safeName;
        $targetFs = $uploadDirFs . $fileName;

        if (move_uploaded_file($_FILES["assignment_file"]["tmp_name"], $targetFs)) {
            $filePathForDb = $uploadDirDb . $fileName;
        } else {
            $_SESSION["flash_error"] = "File upload failed. Please try again.";
            header("Location: ../create-assignment.php");
            exit();
        }

    } else {
        $_SESSION["flash_error"] = "Upload error code: " . (int)$_FILES["assignment_file"]["error"];
        header("Location: ../create-assignment.php");
        exit();
    }
}

/*
|--------------------------------------------------------------------------
| 4) Insert assignment (WITH file_path)
|--------------------------------------------------------------------------
*/
$sql = "
INSERT INTO assignments (unit_id, lecturer_id, title, description, due_date, file_path)
VALUES (?, ?, ?, ?, ?, ?)
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param(
    $stmt,
    "iissss",
    $unitId,
    $lecturerUserId,
    $title,
    $description,
    $dueDate,
    $filePathForDb
);

mysqli_stmt_execute($stmt);

$_SESSION["flash_success"] = "Assignment created successfully.";
header("Location: ../lecturer-assignments.php");
exit();
