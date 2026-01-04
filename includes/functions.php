<?php
// =====================================================
// ZEREK PROJECT — FUNCTIONS FILE
// Handles: users, authentication, validation
// =====================================================

/*
|--------------------------------------------------------------------------
| GET USER BY ID
|--------------------------------------------------------------------------
*/
function getUser($conn, $userId){
    $sql = "SELECT * FROM users WHERE user_id = ?;";
    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt,$sql)){
        echo "<p>Error: Could not load user.</p>";
        exit();
    }

    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    return mysqli_fetch_assoc($result) ?: false;
}

/*
|--------------------------------------------------------------------------
| REGISTER USER (WITH DUPLICATE CHECK)
|--------------------------------------------------------------------------
| IMPORTANT:
| - Prevents duplicate username/email
| - Avoids MySQL fatal error
*/
function registerUser($conn, $username, $password, $role, $firstName, $lastName, $dob, $email){

    // ✅ CHECK IF USER ALREADY EXISTS
    if (userExists($conn, $username) || userExists($conn, $email)) {
        header("location: ../register.php?error=useralreadyexists");
        exit();
    }

    $sql = "
        INSERT INTO users 
        (`username`, `password`, `role`, `name`, `surname`, `date_of_birth`, `email`)
        VALUES (?, ?, ?, ?, ?, ?, ?);
    ";

    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)){
        header("location: ../register.php?error=stmtfailed");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    mysqli_stmt_bind_param(
        $stmt,
        "sssssss",
        $username,
        $hashedPassword,
        $role,
        $firstName,
        $lastName,
        $dob,
        $email
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

/*
|--------------------------------------------------------------------------
| LOGIN USER
|--------------------------------------------------------------------------
*/
function login($conn, $usernameOrEmail, $password){

    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../login.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $usernameOrEmail, $usernameOrEmail);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$user = mysqli_fetch_assoc($result)) {
        header("location: ../login.php?error=usernotfound");
        exit();
    }

    // PASSWORD CHECK
    if (!password_verify($password, $user["password"])) {
        header("location: ../login.php?error=wrongpassword");
        exit();
    }

    session_start();

    // BASE SESSION
    $_SESSION["userId"] = $user["user_id"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["role"] = $user["role"];
    $_SESSION["email"] = $user["email"];

    // LECTURER EXTRA SESSION
    if ($user["role"] === "Lecturer") {

        $sqlLect = "SELECT lecturer_id FROM lecturers WHERE email = ?";
        $stmtLect = mysqli_prepare($conn, $sqlLect);
        mysqli_stmt_bind_param($stmtLect, "s", $user["email"]);
        mysqli_stmt_execute($stmtLect);
        $resLect = mysqli_stmt_get_result($stmtLect);

        if ($lect = mysqli_fetch_assoc($resLect)) {
            $_SESSION["lecturer_id"] = $lect["lecturer_id"];
        } else {
            // DATA INCONSISTENCY WARNING
            die("Lecturer role exists, but lecturer record not found.");
        }
    }

    // ROLE REDIRECT
    if ($user["role"] === "Admin") {
        header("Location: ../admin-dashboard.php");
    } elseif ($user["role"] === "Lecturer") {
        header("Location: ../profile.php"); // your lecturer view is here
    } else {
        header("Location: ../profile.php");
    }

    exit();
}

/*
|--------------------------------------------------------------------------
| CHECK IF USER EXISTS (USERNAME OR EMAIL)
|--------------------------------------------------------------------------
*/
function userExists($conn, $usernameOrEmail){
    $sql = "
        SELECT user_id, username, email, password, role
        FROM users
        WHERE username = ? OR email = ?;
    ";

    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt,$sql)){
        header("location: ../login.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $usernameOrEmail, $usernameOrEmail);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    return mysqli_fetch_assoc($result) ?: false;
}

/*
|--------------------------------------------------------------------------
| UPDATE USER PROFILE
|--------------------------------------------------------------------------
*/
function updateUser($conn, $userId, $firstName, $lastName, $email, $dob){

    $sql = "
        UPDATE users 
        SET name = ?, surname = ?, email = ?, date_of_birth = ?
        WHERE user_id = ?;
    ";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../profile.php?error=updatefailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ssssi", $firstName, $lastName, $email, $dob, $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

/*
|--------------------------------------------------------------------------
| VALIDATION HELPERS
|--------------------------------------------------------------------------
*/
function emptyRegistrationInput($username, $password, $role, $firstName, $lastName, $dob, $email){
    return empty($username) || empty($password) || empty($role)
        || empty($firstName) || empty($lastName)
        || empty($dob) || empty($email);
}

function invalidUsername($username){
    return !preg_match("/^[a-zA-Z0-9]*$/", $username);
}

function passwordsDoNotMatch($password, $confpass){
    return $password !== $confpass;
}
