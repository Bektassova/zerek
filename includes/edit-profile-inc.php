<?php
session_start();

// Check if the user actually clicked the "Save Changes" button
if (isset($_POST["submit"])) {

    // 1. Link our connection and functions files
    require_once 'dbh.php';
    require_once 'functions.php';

    // 2. Grab the data from the $_POST array (from the form)
    $firstName = $_POST["name"];
    $lastName = $_POST["surname"];
    $email = $_POST["email"];
    $dob = $_POST["dob"]; // The new Date of Birth field
    $userId = $_SESSION["userId"]; // The ID of the person currently logged in

    // 3. Simple Validation: Make sure they didn't leave anything empty
    if (empty($firstName) || empty($lastName) || empty($email) || empty($dob)) {
        header("location: ../edit-profile.php?error=emptyinput");
        exit();
    }

    // 4. Run the update function (Make sure this matches the one in functions.php!)
    updateUser($conn, $userId, $firstName, $lastName, $email, $dob);

    // 5. Success! Send them back to the profile to see the changes
    header("location: ../profile.php?update=success");
    exit();

} else {
    // If they tried to access this file without clicking the button, kick them back
    header("location: ../profile.php");
    exit();
}