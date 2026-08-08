<?php
session_start();
require_once "includes/dbh.php";

/*
|--------------------------------------------------------------------------
| STUDENT TIMETABLE
|--------------------------------------------------------------------------
| Displays:
|  - Course timetable
|  - Personal timetable (optional)
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["userId"])) {
    header("Location: login.php");
    exit();
}

$userId = (int)$_SESSION["userId"];

/*
|--------------------------------------------------------------------------
| Get student's course
|--------------------------------------------------------------------------
*/

$courseId = 0;

$sqlCourse = "SELECT course_id FROM users WHERE user_id = ?";

$stmtCourse = mysqli_prepare($conn, $sqlCourse);
mysqli_stmt_bind_param($stmtCourse, "i", $userId);
mysqli_stmt_execute($stmtCourse);

$resCourse = mysqli_stmt_get_result($stmtCourse);

if ($resCourse && mysqli_num_rows($resCourse) > 0) {

    $course = mysqli_fetch_assoc($resCourse);

    $courseId = (int)$course["course_id"];
}

mysqli_stmt_close($stmtCourse);

/*
|--------------------------------------------------------------------------
| Load timetable
|--------------------------------------------------------------------------
*/

$sql = "

SELECT *

FROM timetable

WHERE course_id = ?

   OR user_id = ?

ORDER BY

FIELD(class_day,

'Monday',

'Tuesday',

'Wednesday',

'Thursday',

'Friday'),

start_time

";

$stmt = mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param($stmt,"ii",$courseId,$userId);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

/*
|--------------------------------------------------------------------------
| Build weekly timetable
|--------------------------------------------------------------------------
*/

$week = [

'Monday'=>[],
'Tuesday'=>[],
'Wednesday'=>[],
'Thursday'=>[],
'Friday'=>[]

];

while($row=mysqli_fetch_assoc($result)){

    $week[$row['class_day']][]=$row;

}

include "includes/header.php";

?>

<div class="container mt-5">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>

<i class="fas fa-calendar-alt text-primary"></i>

My Weekly Timetable

</h2>

<a href="profile.php"

class="btn btn-outline-secondary">

Back to Profile

</a>

</div>

<div class="row">

<?php

$days=[

'Monday',

'Tuesday',

'Wednesday',

'Thursday',

'Friday'

];

foreach($days as $day):

?>

<div class="col-lg col-md-6 mb-4">

<div class="card shadow-sm h-100">

<div class="card-header bg-primary text-white text-center">

<h5 class="mb-0">

<?php echo $day; ?>

</h5>

</div>

<div class="card-body">

<?php

if(empty($week[$day])){

?>

<div class="text-center text-muted mt-4">

No classes

</div>

<?php

}else{

foreach($week[$day] as $lesson){

$subject=htmlspecialchars($lesson['subject_name']);

$room=htmlspecialchars($lesson['room_number']);

$type=htmlspecialchars($lesson['class_type']);

$start=date("H:i",strtotime($lesson['start_time']));

$end=date("H:i",strtotime($lesson['end_time']));
?>
<div class="border rounded p-3 mb-3 bg-light lesson-card">

    <div class="fw-bold text-primary fs-5 subject-title">
        <i class="fas fa-book"></i>
        <?php echo $subject; ?>
    </div>

    <div class="mt-2">
        <i class="fas fa-clock text-secondary"></i>
        <?php echo $start; ?> - <?php echo $end; ?>
    </div>

    <div class="mt-2">

        <?php if(strtolower(trim($room))=="online"){ ?>

            <a href="https://teams.microsoft.com"
               target="_blank"
               class="btn btn-success btn-sm">

                <i class="fas fa-video"></i>

                Join Teams

            </a>

        <?php }else{ ?>

            <span class="badge bg-light text-dark border">

                <i class="fas fa-map-marker-alt"></i>

                <?php echo $room; ?>

            </span>

        <?php } ?>

    </div>

    <div class="mt-2">

        <?php

        $badge="bg-primary";

        switch(strtolower($type)){

            case "lecture":
                $badge="bg-primary";
                break;

            case "practical":
                $badge="bg-success";
                break;

            case "workshop":
                $badge="bg-warning text-dark";
                break;

            case "seminar":
                $badge="bg-info text-dark";
                break;

            default:
                $badge="bg-secondary";

        }

        ?>

        <span class="badge <?php echo $badge; ?>">

            <?php echo $type; ?>

        </span>

    </div>

</div>

<?php

}

}

?>

</div>

</div>

</div>

<?php endforeach; ?>

</div>

</div>

<style>

.card-header{

    font-size:20px;

    font-weight:600;

}

.card{

    border-radius:15px;

}

.border.rounded{

    transition:0.25s;

}

.border.rounded:hover{

    transform:translateY(-3px);

    box-shadow:0 6px 16px rgba(0,0,0,.15);

}

.badge{

    font-size:13px;

}

.btn-success{

    width:100%;

}

@media(max-width:768px){

.card{

margin-bottom:20px;

}

}

</style>

<?php include "includes/footer.php"; ?>