<?php
session_start();
require_once "dbh.php";

// Security: only logged-in users
if (!isset($_SESSION['userId'])) {
    header("Location: ../login.php");
    exit();
}

$userId = (int) $_SESSION['userId'];

if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    header("Location: ../profile.php");
    exit();
}

// Save folder (inside includes/uploads/profile_photos/)
$dirFs = __DIR__ . "/uploads/profile_photos/";
if (!is_dir($dirFs)) {
    mkdir($dirFs, 0777, true);
}

$ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
$allowed = ['jpg','jpeg','png','webp'];

if (!in_array($ext, $allowed)) {
    $_SESSION['flash_error'] = "Only JPG, PNG, WEBP allowed.";
    header("Location: ../profile.php");
    exit();
}

$filename = "user_" . $userId . "_" . time() . "." . $ext;
$targetFs = $dirFs . $filename;

if (!move_uploaded_file($_FILES['photo']['tmp_name'], $targetFs)) {
    $_SESSION['flash_error'] = "Upload failed.";
    header("Location: ../profile.php");
    exit();
}

// What we store in DB (relative path from site root)
$pathForDb = "includes/uploads/profile_photos/" . $filename;

$stmt = mysqli_prepare($conn, "UPDATE users SET profile_photo=? WHERE user_id=?");
mysqli_stmt_bind_param($stmt, "si", $pathForDb, $userId);
mysqli_stmt_execute($stmt);

$_SESSION['flash_success'] = "Photo updated.";
header("Location: ../profile.php");
exit();
