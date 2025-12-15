<?php
$conn = mysqli_connect("127.0.0.1", "root", "root", "SchoolManagement");

if (!$conn) {
    die("DB ERROR: " . mysqli_connect_error());
}

echo "DB CONNECTED OK";
