<?php
session_start();
require_once "includes/dbh.php";

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
    header("location: login.php?error=notauthorized");
    exit();
}

$lecturerId = $_SESSION["userId"];

/*
====================================================
GET UNIT ID
====================================================
*/

if(!isset($_GET["unit_id"])){
    die("Unit ID is missing.");
}

$unitId=(int)$_GET["unit_id"];

/*
====================================================
GET UNIT
====================================================
*/

$sql="

SELECT

unit_id,
unit_name,
unit_description

FROM units

WHERE unit_id=?

LIMIT 1

";

$stmt=mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param($stmt,"i",$unitId);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

$unit=mysqli_fetch_assoc($result);

if(!$unit){
    die("Unit not found.");
}

/*
====================================================
DEFAULT VALUES
====================================================
*/

$editMode=false;

$itemId=0;

$weekNumber="";

$topic="";

$description="";

$learningObjectives="";

$homework="";

$lectureNotes="";

$powerPoint="";

$tutorial="";

$readingMaterial="";

$quizLink="";

$isPublished=0;

/*
====================================================
EDIT MODE
====================================================
*/

if(isset($_GET["item_id"])){

    $editMode=true;

    $itemId=(int)$_GET["item_id"];

    $sql="

    SELECT *

    FROM thematic_items

    WHERE id=?

    LIMIT 1

    ";

    $stmt=mysqli_prepare($conn,$sql);

    mysqli_stmt_bind_param($stmt,"i",$itemId);

    mysqli_stmt_execute($stmt);

    $result=mysqli_stmt_get_result($stmt);

    if($row=mysqli_fetch_assoc($result)){

        $weekNumber=$row["order_index"];

        $topic=$row["topic_title"];

        $description=$row["description"];

        $learningObjectives=$row["learning_objectives"];

        $homework=$row["homework"];

        $lectureNotes=$row["lecture_notes"];

        $powerPoint=$row["powerpoint"];

        $tutorial=$row["tutorial"];

        $readingMaterial=$row["reading_material"];

        $quizLink=$row["quiz_link"];

        $isPublished=$row["is_published"];

    }

}

require_once "includes/header.php";
?>

<div class="container mt-5">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2>

📅 Weekly Teaching Plan

</h2>

<h5 class="text-muted">

<?php echo htmlspecialchars($unit["unit_name"]); ?>

</h5>

</div>

<a
href="lecturer-weekly-plan.php?unit_id=<?php echo $unitId; ?>"
class="btn btn-secondary">

Back

</a>

</div>

<div class="card shadow">

<div class="card-body">

<form

action="includes/lecturer-weekly-plan-inc.php"

method="POST"

enctype="multipart/form-data">

<input
type="hidden"
name="unit_id"
value="<?php echo $unitId; ?>">

<?php if($editMode){ ?>

<input
type="hidden"
name="item_id"
value="<?php echo $itemId; ?>">

<?php } ?>
<div class="row">

    <div class="col-md-3 mb-3">

        <label class="form-label fw-bold">

            Week Number

        </label>

        <input
            type="number"
            name="week_number"
            class="form-control"
            min="1"
            required
            value="<?php echo htmlspecialchars($weekNumber); ?>">

    </div>

    <div class="col-md-9 mb-3">

        <label class="form-label fw-bold">

            Topic

        </label>

        <input
            type="text"
            name="topic"
            class="form-control"
            required
            value="<?php echo htmlspecialchars($topic); ?>">

    </div>

</div>

<div class="mb-3">

    <label class="form-label fw-bold">

        Description

    </label>

    <textarea
        name="description"
        rows="4"
        class="form-control"><?php echo htmlspecialchars($description); ?></textarea>

</div>

<div class="mb-3">

    <label class="form-label fw-bold">

        Learning Objectives

    </label>

    <textarea
        name="learning_objectives"
        rows="4"
        class="form-control"><?php echo htmlspecialchars($learningObjectives); ?></textarea>

</div>

<div class="mb-4">

    <label class="form-label fw-bold">

        Homework

    </label>

    <textarea
        name="homework"
        rows="4"
        class="form-control"><?php echo htmlspecialchars($homework); ?></textarea>

</div>

<hr>

<h5 class="mb-3">

📚 Learning Materials

</h5>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Lecture Notes

</label>

<input
type="file"
name="lecture_notes"
class="form-control"
accept=".pdf,.doc,.docx">

<?php if($editMode){ ?>

<input
type="hidden"
name="old_lecture_notes"
value="<?php echo htmlspecialchars($lectureNotes); ?>">

<?php } ?>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

PowerPoint

</label>

<input
type="file"
name="powerpoint"
class="form-control"
accept=".ppt,.pptx,.pdf">

<?php if($editMode){ ?>

<input
type="hidden"
name="old_powerpoint"
value="<?php echo htmlspecialchars($powerPoint); ?>">

<?php } ?>

</div>

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Tutorial

</label>

<input
type="file"
name="tutorial"
class="form-control"
accept=".pdf,.doc,.docx">

<?php if($editMode){ ?>

<input
type="hidden"
name="old_tutorial"
value="<?php echo htmlspecialchars($tutorial); ?>">

<?php } ?>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Reading Material

</label>

<input
type="file"
name="reading_material"
class="form-control"
accept=".pdf,.doc,.docx">

<?php if($editMode){ ?>

<input
type="hidden"
name="old_reading_material"
value="<?php echo htmlspecialchars($readingMaterial); ?>">

<?php } ?>

</div>

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Quiz

</label>

<input
type="file"
name="quiz_link"
class="form-control"
accept=".pdf,.doc,.docx">

<?php if($editMode){ ?>

<input
type="hidden"
name="old_quiz_link"
value="<?php echo htmlspecialchars($quizLink); ?>">

<?php } ?>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Status

</label>

<select
name="is_published"
class="form-select">

<option
value="0"
<?php if($isPublished==0) echo "selected"; ?>>

Draft

</option>

<option
value="1"
<?php if($isPublished==1) echo "selected"; ?>>

Published

</option>

</select>

</div>

</div>
<hr class="mt-4 mb-4">

<div class="d-flex justify-content-between">

    <a
        href="lecturer-weekly-plan.php?unit_id=<?php echo $unitId; ?>"
        class="btn btn-secondary">

        Cancel

    </a>

    <?php if($editMode){ ?>

        <button
            type="submit"
            name="update_week"
            class="btn btn-primary">

            💾 Update Week

        </button>

    <?php }else{ ?>

        <button
            type="submit"
            name="save_week"
            class="btn btn-success">

            ➕ Save Week

        </button>

    <?php } ?>

</div>

</form>

</div>

</div>

</div>

<?php require_once "includes/footer.php"; ?>