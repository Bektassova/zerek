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
        $_SESSION["role"] !== "Lecturer" &&
        $_SESSION["role"] !== "Teacher"
    )
){
    header("Location: login.php");
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

$unitId = (int)$_GET["unit_id"];

/*
====================================================
GET UNIT INFORMATION
====================================================
*/

$sql = "

SELECT
    unit_id,
    unit_name,
    unit_description

FROM units

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

$result = mysqli_stmt_get_result($stmt);

$unit = mysqli_fetch_assoc($result);

if(!$unit){

    die("Unit not found.");

}

/*
====================================================
GET THEMATIC PLAN
====================================================
*/

$sqlPlan = "

SELECT id

FROM thematic_plans

WHERE unit_id = ?

LIMIT 1

";

$stmtPlan = mysqli_prepare($conn,$sqlPlan);

mysqli_stmt_bind_param(
    $stmtPlan,
    "i",
    $unitId
);

mysqli_stmt_execute($stmtPlan);

$planResult = mysqli_stmt_get_result($stmtPlan);

$plan = mysqli_fetch_assoc($planResult);

$planId = $plan ? $plan["id"] : 0;

/*
====================================================
LOAD ALL WEEKS
====================================================
*/

$sqlWeeks = "

SELECT *

FROM thematic_items

WHERE plan_id = ?

ORDER BY order_index ASC

";

$stmtWeeks = mysqli_prepare($conn,$sqlWeeks);

mysqli_stmt_bind_param(
    $stmtWeeks,
    "i",
    $planId
);

mysqli_stmt_execute($stmtWeeks);

$weeksResult = mysqli_stmt_get_result($stmtWeeks);

require_once "includes/header.php";
?>

<div class="container mt-5">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2 class="fw-bold">

<?php echo htmlspecialchars($unit["unit_name"]); ?>

</h2>

<p class="text-muted">

<?php
if(!empty($unit["unit_description"])){

    echo htmlspecialchars($unit["unit_description"]);

}else{

    echo "No description available.";

}
?>

</p>

</div>

<a
href="lecturer-units.php"
class="btn btn-secondary">

← Back

</a>

</div>

<div class="text-end mb-4">

<a
href="lecturer-weekly-plan-form.php?unit_id=<?php echo $unitId; ?>"
class="btn btn-success">

➕ Add Week

</a>

</div>

<div class="card shadow">

<div class="card-body">

<table class="table table-hover align-middle">

<thead class="table-primary">

<tr>

<th style="width:120px;">Week</th>

<th>Topic</th>

<th style="width:220px;">Materials</th>

<th style="width:140px;">Status</th>

<th style="width:120px;">Actions</th>

</tr>

</thead>

<tbody>
    <?php if(mysqli_num_rows($weeksResult) > 0){ ?>

<?php while($lesson = mysqli_fetch_assoc($weeksResult)){ ?>

<tr>

<td>

<strong>

Week <?php echo $lesson["order_index"]; ?>

</strong>

</td>

<td>

<strong>

<?php echo htmlspecialchars($lesson["topic_title"]); ?>

</strong>

<br>

<small class="text-muted">

<?php

if(!empty($lesson["description"])){

    echo htmlspecialchars($lesson["description"]);

}else{

    echo "No description";

}

?>

</small>

</td>

<td>

<?php



$hasFiles = false;

if(!empty($lesson["lecture_notes"])){

    $hasFiles = true;

    echo '<div>
            📄 <a href="'.htmlspecialchars($lesson["lecture_notes"]).'"
            target="_blank">
            Lecture Notes
            </a>
          </div>';

}

if(!empty($lesson["powerpoint"])){

    $hasFiles = true;

    echo '<div>
            📊 <a href="'.htmlspecialchars($lesson["powerpoint"]).'"
            target="_blank">
            PowerPoint
            </a>
          </div>';

}

if(!empty($lesson["tutorial"])){

    $hasFiles = true;

    echo '<div>
            📝 <a href="'.htmlspecialchars($lesson["tutorial"]).'"
            target="_blank">
            Tutorial
            </a>
          </div>';

}

if(!empty($lesson["reading_material"])){

    $hasFiles = true;

    echo '<div>
            📖 <a href="'.htmlspecialchars($lesson["reading_material"]).'"
            target="_blank">
            Reading Material
            </a>
          </div>';

}

if(!empty($lesson["quiz_link"])){

    $hasFiles = true;

    echo '<div>
            ✅ <a href="'.htmlspecialchars($lesson["quiz_link"]).'"
            target="_blank">
            Quiz
            </a>
          </div>';

}

if(!$hasFiles){

    echo '<span class="badge bg-secondary">No files</span>';

}

?>


</td>
<td>

<form
    action="includes/toggle-week-publish.php"
    method="POST"
    style="display:inline;"
>

<input
    type="hidden"
    name="item_id"
    value="<?php echo $lesson["id"]; ?>"
>

<input
    type="hidden"
    name="unit_id"
    value="<?php echo $unitId; ?>"
>

<?php if($lesson["is_published"] == 1){ ?>

<button
    type="submit"
    class="btn btn-sm btn-success"
>
    Published
</button>

<?php }else{ ?>

<button
    type="submit"
    class="btn btn-sm btn-warning"
>
    Publish
</button>

<?php } ?>

</form>

</td>


<td>

<a
href="lecturer-weekly-plan-form.php?unit_id=<?php echo $unitId; ?>&item_id=<?php echo $lesson["id"]; ?>"
class="btn btn-sm btn-primary">

Edit

</a>

</td>

</tr>

<?php } ?>

<?php }else{ ?>

<tr>

<td colspan="5" class="text-center text-muted">

No weeks have been added yet.

</td>

</tr>

<?php } ?>
</tbody>

</table>

</div>

</div>

<div class="mt-4 d-flex justify-content-between">

<a
href="lecturer-units.php"
class="btn btn-secondary">

← Back to My Units

</a>

<a
href="lecturer-weekly-plan-form.php?unit_id=<?php echo $unitId; ?>"
class="btn btn-success">

➕ Add Another Week

</a>

</div>

</div>

<?php require_once "includes/footer.php"; ?>