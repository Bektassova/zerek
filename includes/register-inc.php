<?php 
    require_once "dbh.php";// <--- Connection established here
    require_once "functions.php";// <--- functions are loaded
     $conn = connect_db();

    if(!isset($_POST["submit"])){
        header("location: ../register.php");
        exit();
    }
    else{ 
        //all 7 required variables from the form
        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $confpass = $_POST["confpass"];
        $firstName = $_POST["name"];
        $lastName = $_POST["surname"];
        $role = $_POST["role"];
        $dob = $_POST["dob"];

        $error_url = ""; // New variable to collect errors

       // ... all validation functions here ...
        // (all error checks are above the exit/redirect line)
        
      


       // 2. RUN VALIDATION CHECKS

        // CHECK 1: Empty Fields (MUST have 7 arguments here)
        if(emptyRegistrationInput($username, $password, $role, $firstName, $lastName, $dob, $email)){
            $error_url .= "emptyinput=true&";
        }
        
        // CHECK 2: Invalid Username
        if(invalidUsername($username)){
            $error_url .= "invalidUsername=true&";
        }

        // CHECK 3: Passwords Don't Match
        if(passwordsDoNotMatch($password, $confpass)){
            $error_url .= "passwordsDoNotMatch=true&";
        }

        // CHECK 4: Invalid Email
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
             $error_url .= "invalidEmail=true&";
        }


        // 3. IF ANY ERRORS EXIST, REDIRECT
       // 3. IF ANY ERRORS EXIST, REDIRECT
        if(!empty($error_url)){ // Use !empty() instead of checking for != ""
            // Redirect with all accumulated errors, removing the trailing '&'
            header("location: ../register.php?error=true&" . rtrim($error_url, '&'));
            exit();
        }

        // 4. IF NO ERRORS, CONTINUE TO REGISTRATION
        
        // 1. Register the user (saves data to DB)
        registerUser($conn, $username, $password, $role, $firstName, $lastName, $dob, $email);

        // 2. Immediately log the user in by fetching the ID we just created
        $user = userExists($conn, $username); // or use $email here

        // 3. Set Session Variables and Redirect to Profile
        if($user) {
            session_start();
            $_SESSION["username"] = $username;
            $_SESSION["userId"] = $user["user_id"]; 
            $_SESSION["role"] = $role; 
            
            header("location: ../profile.php");
            exit();
        } else {
             // Fallback if we can't find the user we just registered
             header("location: ../login.php?error=autologinfail");
             exit();
        }
    }
?>