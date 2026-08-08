<?php
session_start();

require_once "dbh.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

error_reporting(E_ALL);

ini_set('display_errors',1);
/*
====================================================
SECURITY
====================================================
*/

if (
    !isset($_SESSION["userId"]) ||
    (
        $_SESSION["role"] != "Lecturer" &&
        $_SESSION["role"] != "Teacher"
    )
){
    header("Location: ../login.php");
    exit();
}

$lecturerId = $_SESSION["userId"];

/*
====================================================
ONLY ALLOW SAVE
====================================================
*/

if(
    !isset($_POST["save_week"]) &&
    !isset($_POST["update_week"])
){
    header("Location: ../profile.php");
    exit();
}

/*
====================================================
RECEIVE FORM DATA
====================================================
*/

$unitId = (int)$_POST["unit_id"];

$weekNumber = (int)$_POST["week_number"];

$topic = trim($_POST["topic"]);

$description = trim($_POST["description"]);

$learningObjectives =
trim($_POST["learning_objectives"]);

$homework =
trim($_POST["homework"]);

$isPublished =
isset($_POST["is_published"])
?
(int)$_POST["is_published"]
:
0;


/*
====================================================
FIND THEMATIC PLAN
====================================================
*/

$sql = "

SELECT id

FROM thematic_plans

WHERE unit_id = ?

LIMIT 1

";

$stmt = mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(
$stmt,
"i",
$unitId
);

mysqli_stmt_execute($stmt);

$result =
mysqli_stmt_get_result($stmt);

if($plan=mysqli_fetch_assoc($result))
{

    $planId=$plan["id"];

}
else
{

    $title="Weekly Plan";

    $sql="

    INSERT INTO thematic_plans
    (
        unit_id,
        title,
        created_by
    )

    VALUES
    (
        ?,
        ?,
        ?
    )

    ";

    $stmt=mysqli_prepare($conn,$sql);

    mysqli_stmt_bind_param(

        $stmt,

        "isi",

        $unitId,

        $title,

        $lecturerId

    );

    mysqli_stmt_execute($stmt);

    $planId=mysqli_insert_id($conn);

}

/*
====================================================
UPLOAD DIRECTORY
====================================================
*/

$uploadDir="../uploads/weekly-plan/";

if(!is_dir($uploadDir))
{
    mkdir($uploadDir,0777,true);
}

/*
====================================================
UPLOAD FUNCTION
====================================================
*/

function uploadFile($field,$uploadDir)
{

    if(
        !isset($_FILES[$field]) ||
        $_FILES[$field]["error"]!=0
    )
    {
        return "";
    }

    $fileName=
    time().
    "_".
    basename($_FILES[$field]["name"]);

    move_uploaded_file(

        $_FILES[$field]["tmp_name"],

        $uploadDir.$fileName

    );

    return
    "uploads/weekly-plan/".$fileName;

}

/*
====================================================
UPLOAD FILES
====================================================
*/

$lectureNotes =
uploadFile(
"lecture_notes",
$uploadDir
);

$powerPoint =
uploadFile(
"powerpoint",
$uploadDir
);

$tutorial =
uploadFile(
"tutorial",
$uploadDir
);

$readingMaterial =
uploadFile(
"reading_material",
$uploadDir
);

$quizLink =
uploadFile(
"quiz_link",
$uploadDir
);

/*
====================================================
PART 2 STARTS HERE
====================================================
*/
/*
====================================================
UPDATE EXISTING WEEK
====================================================
*/

if (isset($_POST["update_week"])) {

    $itemId = (int)$_POST["item_id"];

    // Keep old files if no new file uploaded
    if (empty($lectureNotes)) {
        $lectureNotes = $_POST["old_lecture_notes"];
    }

    if (empty($powerPoint)) {
        $powerPoint = $_POST["old_powerpoint"];
    }

    if (empty($tutorial)) {
        $tutorial = $_POST["old_tutorial"];
    }

    if (empty($readingMaterial)) {
        $readingMaterial = $_POST["old_reading_material"];
    }

    if (empty($quizLink)) {
        $quizLink = $_POST["old_quiz_link"];
    }

    $sql = "

    UPDATE thematic_items

    SET
        order_index=?,
        topic_title=?,
        description=?,
        learning_objectives=?,
        homework=?,
        lecture_notes=?,
        powerpoint=?,
        tutorial=?,
        reading_material=?,
        quiz_link=?,
        is_published=?

    WHERE id=?

    ";

    $stmt = mysqli_prepare($conn,$sql);

    mysqli_stmt_bind_param(

        $stmt,

        "isssssssssii",

        $weekNumber,
        $topic,
        $description,
        $learningObjectives,
        $homework,
        $lectureNotes,
        $powerPoint,
        $tutorial,
        $readingMaterial,
        $quizLink,
        $isPublished,
        $itemId

    );

    mysqli_stmt_execute($stmt);

}

/*
====================================================
INSERT NEW WEEK
====================================================
*/

else {

    $sql = "

    INSERT INTO thematic_items
    (
        plan_id,
        topic_title,
        order_index,
        description,
        learning_objectives,
        homework,
        lecture_notes,
        powerpoint,
        tutorial,
        reading_material,
        quiz_link,
        is_published
    )

    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?
    )

    ";

    $stmt = mysqli_prepare($conn,$sql);

    mysqli_stmt_bind_param(

        $stmt,

        "isissssssssi",

        $planId,
        $topic,
        $weekNumber,
        $description,
        $learningObjectives,
        $homework,
        $lectureNotes,
        $powerPoint,
        $tutorial,
        $readingMaterial,
        $quizLink,
        $isPublished

    );

mysqli_stmt_execute($stmt);
}
/*
====================================================
RETURN
====================================================
*/

header(
    "Location: ../lecturer-weekly-plan.php?unit_id=".$unitId
);

exit();