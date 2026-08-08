<?php
session_start();

require_once "dbh.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
====================================================
SECURITY
====================================================
*/

if (
    !isset($_SESSION["userId"]) ||
    (
        $_SESSION["role"] !== "Lecturer" &&
        $_SESSION["role"] !== "Teacher"
    )
) {
    header("Location: ../login.php");
    exit();
}

/*
====================================================
GET DATA
====================================================
*/

if (
    !isset($_POST["item_id"]) ||
    !isset($_POST["unit_id"])
) {
    header("Location: ../profile.php");
    exit();
}

$itemId = (int)$_POST["item_id"];
$unitId = (int)$_POST["unit_id"];

/*
====================================================
TOGGLE PUBLISHED STATUS
====================================================
*/

$sql = "
    UPDATE thematic_items
    SET is_published = IF(is_published = 1, 0, 1)
    WHERE id = ?
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $itemId
);

mysqli_stmt_execute($stmt);

/*
====================================================
RETURN TO WEEKLY PLAN
====================================================
*/

header(
    "Location: ../lecturer-weekly-plan.php?unit_id=" . $unitId
);

exit();