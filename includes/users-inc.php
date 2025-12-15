<?php require_once "dbh.php";
require_once "functions.php";

$result = getUsers($conn);
?>
<?php


while($row = mysqli_fetch_assoc($result)) {
    $user_id = $row['user_id'];
    $username = $row['username'];
    $password = $row['password'];
    $name = $row['name'];
    $surname = $row['surname'];
    $date_of_birth = $row['date_of_birth'];

    echo "<div class='col'>";
    echo "<h2>{$username}</h2><h4>{$name} {$surname}</h4><p>({$date_of_birth})</p>";
    echo "</div>";
}
?>





