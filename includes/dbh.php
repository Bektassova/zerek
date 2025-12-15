<?php
function connect_db() {
    $server = "127.0.0.1";
    $username = "root";
    $password = "root";
    $dbName = "SchoolManagement";
    $port = 8889;

    $conn = mysqli_connect($server, $username, $password, $dbName, $port);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
}
