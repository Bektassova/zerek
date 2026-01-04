<?php
require_once "dbh.php";
require_once "functions.php";

if (!isset($_POST["submit"])) {
    header("location: ../register.php");
    exit();
}

// ========================
// 1. GET FORM DATA
// ========================
$username   = $_POST["username"];
$email      = $_POST["email"];
$password   = $_POST["password"];
$confpass   = $_POST["confpass"];
$firstName  = $_POST["name"];
$lastName   = $_POST["surname"];
$role       = $_POST["role"];
$dob        = $_POST["dob"];

$error_url = "";

// ========================
// 2. VALIDATION
// ========================
if (emptyRegistrationInput($username, $password, $role, $firstName, $lastName, $dob, $email)) {
    $error_url .= "emptyinput=true&";
}

if (invalidUsername($username)) {
    $error_url .= "invalidUsername=true&";
}

if (passwordsDoNotMatch($password, $confpass)) {
    $error_url .= "passwordsDoNotMatch=true&";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error_url .= "invalidEmail=true&";
}

if (!empty($error_url)) {
    header("location: ../register.php?error=true&" . rtrim($error_url, '&'));
    exit();
}

// ========================
// 3. REGISTER USER (USERS)
// ========================
registerUser($conn, $username, $password, $role, $firstName, $lastName, $dob, $email);

// ========================
// 4. GET NEW USER
// ========================
$user = userExists($conn, $username);

if (!$user) {
    header("location: ../register.php?error=registrationfailed");
    exit();
}

$userId = $user["user_id"];

// ========================
// 5. 🔥 IMPORTANT PART 🔥
// ADD TO LECTURERS IF ROLE = Lecturer
// ========================
if ($role === "Lecturer") {
    $sql = "INSERT INTO lecturers (user_id, name, surname, email)
            VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isss", $userId, $firstName, $lastName, $email);
    mysqli_stmt_execute($stmt);
}

// ========================
// 6. AUTO LOGIN
// ========================
session_start();
$_SESSION["userId"] = $userId;
$_SESSION["username"] = $username;
$_SESSION["role"] = $role;

header("location: ../profile.php");
exit();
