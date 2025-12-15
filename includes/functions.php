<?php
    // This file handles all database interactions for the Zerek project

    function getUsers($conn){
        $sql = "SELECT * FROM users";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt,$sql)){
            echo "<p>Error: Could not load users.</p>";
            exit();
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }

    function getUser($conn, $userId){
        // Changed 'id' to 'user_id' to match your schema
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
    }

    // Updated to include 'role' and 'date_of_birth' and remove nationality/age
    function registerUser($conn, $username, $password, $role, $firstName, $lastName, $dob, $email){
        $sql = "INSERT INTO users (username, password, role, name, surname, date_of_birth, email) VALUES (?,?,?,?,?,?,?);";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt,$sql)){
            header("location: ../register.php?error=stmtfailed");
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // "sssssss" means 7 strings (role and date are passed as strings to MySQL)
        mysqli_stmt_bind_param($stmt, "sssssss", $username, $hashedPassword, $role, $firstName, $lastName, $dob, $email);

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    function login($conn, $username, $password){
        $user = userExists($conn, $username);
        if(!$user){
            header("location: ../login.php?error=incorrectlogin");
            exit();
        }

        $userId = $user["user_id"]; // Changed from id to user_id
        $dbPassword = $user["password"];
        $checkedPassword = password_verify($password, $dbPassword);

        if(!$checkedPassword){
            header("location: ../login.php?error=incorrectlogin");
            exit();
        }

        session_start();
        $_SESSION["username"] = $username;
        $_SESSION["userId"] = $userId;
        $_SESSION["role"] = $user["role"]; // Store role in session

        header("location: ../profile.php");
        exit();
    }

    function userExists($conn, $username){
        // Changed id to user_id and added password/role for the login function to use
       $sql = "SELECT user_id, username, email, password, role FROM users WHERE username = ? OR email = ?;";

        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt,$sql)){
            header("location: ../login.php?error=stmtfailed");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "ss", $username, $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);

        if($row = mysqli_fetch_assoc($result)){
            return $row;
        } else {
            return false;
        }
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