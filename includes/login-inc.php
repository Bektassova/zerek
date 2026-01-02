<?php 



// This file is executed when the login form is submitted

// Check if the form was actually submitted
if(!isset($_POST["submit"])){
    // If not submitted, redirect back to the login page
    header("location: ../login.php");
    exit();
}
else {
    // Get the user's submitted credentials using the form input names: 'uid' and 'pwd'
    $usernameOrEmail = $_POST["uid"]; // The user can enter a username OR email
    $password = $_POST["pwd"];        // The password

    // --- Validation Start ---
    
    // Check for empty inputs
    if(empty($usernameOrEmail) || empty($password)){
        header("location: ../login.php?error=emptyinput");
        exit();
    }
    
    // --- Validation End ---

    require_once "dbh.php";
    require_once "functions.php";

    // The login function (defined in functions.php) handles checking the password hash
    // and starting the session.
    login($conn, $usernameOrEmail, $password);
}
?>