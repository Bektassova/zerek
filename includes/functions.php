<?php
    // This file handles all database interactions for the Zerek project
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

        if($row = mysqli_fetch_assoc($result)){
            return $row;
        } else {
            return false;
        }
    } // Ensure this bracket exists!

    function registerUser($conn, $username, $password, $role, $firstName, $lastName, $dob, $email){
        // Using backticks to prevent the "Unknown Column" error from before
        $sql = "INSERT INTO users (`username`, `password`, `role`, `name`, `surname`, `date_of_birth`, `email`) VALUES (?, ?, ?, ?, ?, ?, ?);";
        
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)){
            header("location: ../register.php?error=stmtfailed");
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "sssssss", $username, $hashedPassword, $role, $firstName, $lastName, $dob, $email);

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    function login($conn, $usernameOrEmail, $password)
{
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../login.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $usernameOrEmail, $usernameOrEmail);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {

        // Verify password
        if (!password_verify($password, $user["password"])) {
            header("location: ../login.php?error=wrongpassword");
            exit();
        }

        session_start();

        // === BASE SESSION ===
        $_SESSION["userId"] = $user["user_id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["email"] = $user["email"];

        // === LECTURER EXTRA LOGIC ===
        if ($user["role"] === "Lecturer") {

            $sqlLect = "SELECT lecturer_id FROM lecturers WHERE email = ?";
            $stmtLect = mysqli_prepare($conn, $sqlLect);
            mysqli_stmt_bind_param($stmtLect, "s", $user["email"]);
            mysqli_stmt_execute($stmtLect);
            $resLect = mysqli_stmt_get_result($stmtLect);

            if ($lect = mysqli_fetch_assoc($resLect)) {
                $_SESSION["lecturer_id"] = $lect["lecturer_id"];
            } else {
                die("Lecturer role exists, but lecturer record not found.");
            }
        }

        // === REDIRECT BY ROLE ===
        if ($user["role"] === "Admin") {
            header("Location: ../admin-dashboard.php");
        } elseif ($user["role"] === "Lecturer") {
            header("Location: ../lecturer-dashboard.php");
        } else {
            header("Location: ../student-dashboard.php");
        }

        exit();
    }
    else {
        header("location: ../login.php?error=usernotfound");
        exit();
    }
}

  function userExists($conn, $usernameOrEmail){
    $sql = "SELECT user_id, username, email, password, role 
            FROM users 
            WHERE username = ? OR email = ?;";

    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt,$sql)){
        header("location: ../login.php?error=stmtfailed");
        exit();
    }

    // IMPORTANT: the same value twice. ВАЖНО: одно и то же значение два раза
   mysqli_stmt_bind_param($stmt, "ss", $usernameOrEmail, $usernameOrEmail);


    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    if($row = mysqli_fetch_assoc($result)){
        return $row;
    } else {
        return false;
    }
}

    function updateUser($conn, $userId, $firstName, $lastName, $email, $dob) {
    // We added date_of_birth = ? to the SQL query
    $sql = "UPDATE users SET name = ?, surname = ?, email = ?, date_of_birth = ? WHERE user_id = ?;";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../profile.php?error=updatefailed");
        exit();
    }

    // "ssssi" means 4 strings and 1 integer (userId)
    mysqli_stmt_bind_param($stmt, "ssssi", $firstName, $lastName, $email, $dob, $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

    // Validation functions updated for your fields
    function emptyRegistrationInput($username, $password, $role, $firstName, $lastName, $dob, $email){
        if(empty($username) || empty($password) || empty($role) || empty($firstName) || empty($lastName) || empty($dob) || empty($email)){
            return true;
        }
        return false;
    }

    function invalidUsername($username){
        if(!preg_match("/^[a-zA-Z0-9]*$/",$username)){
            return true;
        }
        return false;
    }

    function passwordsDoNotMatch($password, $confpass){
        return ($password !== $confpass);
    }
?>