<?php
$server = "127.0.0.1";
$dbUsername = "root";
$dbPassword = "root";
$dbName = "SchoolManagement";
$port = 8889;

$conn = mysqli_connect($server, $dbUsername, $dbPassword, $dbName, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


   


   