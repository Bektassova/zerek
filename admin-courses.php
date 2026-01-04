<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include "includes/header.php";

require_once 'includes/dbh.php';



// Security check

if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Admin") {

header("location: login.php");

exit();

}



// =========================

// QUERY 1: Active Academic Structure

// =========================

$unitsSql = "

SELECT

units.unit_id,

units.unit_name,

units.unit_description,

courses.course_name

FROM units

LEFT JOIN courses ON units.course_id = courses.course_id

ORDER BY units.unit_name ASC

";

$unitsResult = mysqli_query($conn, $unitsSql);



// =========================

// QUERY 2: Courses + unit count

// =========================

$coursesSql = "

SELECT

courses.course_id,

courses.course_name,

COUNT(units.unit_id) AS unit_count

FROM courses

LEFT JOIN units ON courses.course_id = units.course_id

GROUP BY courses.course_id

ORDER BY courses.course_name ASC

";

$coursesResult = mysqli_query($conn, $coursesSql);

?>



<div class="container mt-5 mb-5">

<div class="row">



<!-- LEFT COLUMN -->

<div class="col-md-4">



<!-- STEP 1: Create Course -->

<div class="card shadow-sm mb-4 border-primary">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">Step 1: Create Course</h5>

</div>

<div class="card-body">

<form action="includes/admin-course-add-inc.php" method="post">

<div class="mb-3">

<label class="form-label fw-bold">Course Name</label>

<input type="text" name="course_name" class="form-control" placeholder="e.g. Business Studies" required>

</div>

<button type="submit" name="submit_course" class="btn btn-primary w-100">

Add Course to System

</button>

</form>

</div>

</div>



<!-- STEP 2: Add Unit to Course -->

<div class="card shadow-sm mb-4 border-success">

<div class="card-header bg-success text-white">

<h5 class="mb-0">Step 2: Add Unit to Course</h5>

</div>

<div class="card-body">

<form action="includes/admin-units-inc.php" method="post">



<div class="mb-3">

<label class="form-label fw-bold">Unit Name</label>

<input type="text" name="unit_name" class="form-control" placeholder="e.g. Management" required>

</div>



<div class="mb-3">

<label class="form-label fw-bold">Assign to Course</label>

<select name="course_id" class="form-select" required>

<option value="">-- Select Course --</option>

<?php

mysqli_data_seek($coursesResult, 0);

while ($course = mysqli_fetch_assoc($coursesResult)): ?>

<option value="<?php echo $course['course_id']; ?>">

<?php echo htmlspecialchars($course['course_name']); ?>

</option>

<?php endwhile; ?>

</select>

</div>



<div class="mb-3">

<label class="form-label fw-bold">Description</label>

<textarea name="unit_description" class="form-control" rows="2"></textarea>

</div>



<button type="submit" name="submit" class="btn btn-success w-100">

Link Unit to Course

</button>

</form>

</div>

</div>



<!-- MANAGE COURSES -->

<div class="card shadow-sm mb-4 border-danger">

<div class="card-header bg-danger text-white py-2">

<h6 class="mb-0">Manage Courses (Delete / Clean-up)</h6>

</div>

<div class="card-body p-0">

<table class="table table-sm mb-0" style="font-size: 0.85rem;">

<thead class="table-light">

<tr>

<th>ID</th>

<th>Course</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

mysqli_data_seek($coursesResult, 0);

while($c = mysqli_fetch_assoc($coursesResult)):

$hasUnits = ($c['unit_count'] > 0);

?>

<tr>

<td><?php echo $c['course_id']; ?></td>

<td><?php echo htmlspecialchars($c['course_name']); ?></td>

<td>

<?php if ($hasUnits): ?>

<button class="btn btn-secondary btn-sm" disabled title="Cannot delete: Course has units">

<i class="fas fa-lock"></i> Locked (<?php echo $c['unit_count']; ?> Units)

</button>

<?php else: ?>

<a href="includes/delete-course-inc.php?id=<?php echo $c['course_id']; ?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Are you sure you want to delete this empty course?')">

Delete

</a>

<?php endif; ?>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

</div>



</div>



<!-- RIGHT COLUMN -->

<div class="col-md-8">



<!-- FLASH MESSAGE -->

<?php if(isset($_SESSION['flash_success'])): ?>

<div class="alert alert-success alert-dismissible fade show" role="alert">

<?php echo $_SESSION['flash_success']; ?>

<button type="button" class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php unset($_SESSION['flash_success']); ?>

<?php endif; ?>



<!-- ACTIVE ACADEMIC STRUCTURE -->

<div class="card shadow-sm mb-4">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">Active Academic Structure</h5>

</div>

<div class="table-responsive">

<table class="table table-hover mb-0">

<thead>

<tr>

<th>Unit</th>

<th>Course</th>

<th>Description</th>

<th>Lecturers</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php while ($row = mysqli_fetch_assoc($unitsResult)): ?>

<tr>

<td><strong><?php echo htmlspecialchars($row['unit_name']); ?></strong></td>

<td>

<span class="badge bg-info text-dark">

<?php echo htmlspecialchars($row['course_name'] ?? 'Unassigned'); ?>

</span>

</td>

<td>

<small><?php echo htmlspecialchars($row['unit_description']); ?></small>

</td>

<td>

<?php
// Мы меняем 'lecturers' на 'users', потому что именно там лежат "паспорта" (user_id = 25)
$lectQuery = "
    SELECT u.name, u.surname
    FROM users u
    JOIN lecturer_units lu ON u.user_id = lu.lecturer_id
    WHERE lu.unit_id = ?
    ORDER BY u.name ASC
";

$stmt = mysqli_prepare($conn, $lectQuery);
mysqli_stmt_bind_param($stmt, "i", $row['unit_id']);
mysqli_stmt_execute($stmt);
$lectResult = mysqli_stmt_get_result($stmt);
$lecturersList = [];

while ($lect = mysqli_fetch_assoc($lectResult)) {
    // Теперь мы берем имена напрямую из таблицы users
    $lecturersList[] = htmlspecialchars($lect['name'] . ' ' . $lect['surname']);
}

// Если в списке что-то есть, выводим через запятую
echo implode(", ", $lecturersList);
?>


</td>

<td class="text-nowrap">

<a href="edit-unit.php?id=<?php echo $row['unit_id']; ?>"

class="btn btn-sm btn-outline-primary me-2">

Edit

</a>



<a href="includes/delete-course-inc.php?id=<?php echo $course['course_id']; ?>&return=admin-courses.php"

class="btn btn-sm btn-outline-danger"

onclick="return confirm('Delete this course?')">

Delete

</a>



</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

</div>

<?php

// Units for Assign Lecturers form (SEPARATE QUERY!)

$unitsForAssignSql = "

SELECT unit_id, unit_name

FROM units

ORDER BY unit_name ASC

";

$unitsForAssignResult = mysqli_query($conn, $unitsForAssignSql);

?>



<!-- ASSIGN LECTURERS TO UNIT -->

<div class="card shadow-sm mb-4">

<div class="card-header bg-secondary text-white">

<h5 class="mb-0">Assign Lecturers to Unit</h5>

</div>

<div class="card-body">

<form method="post" action="includes/assign-lecturers-inc.php">

<div class="mb-3">

<label for="unit_id" class="form-label fw-bold">Select Unit</label>

<select name="unit_id" id="unit_id" class="form-select" required>

<option value="">-- Select Unit --</option>

<?php while ($unit = mysqli_fetch_assoc($unitsForAssignResult)): ?>

<option value="<?php echo $unit['unit_id']; ?>">

<?php echo htmlspecialchars($unit['unit_name']); ?>

</option>

<?php endwhile; ?>

</select>



</div>



<div class="mb-3">

<label for="lecturer_ids" class="form-label fw-bold">Select Lecturer(s)</label>

<select name="lecturer_ids[]" id="lecturer_ids" class="form-select" multiple required>

<?php

// 1. Мы просим систему брать user_id (паспорт 25), а не анкету 9
$lecturerQuery = "SELECT user_id, name, surname FROM users WHERE role = 'Lecturer' ORDER BY name ASC";
$lecturerResult = mysqli_query($conn, $lecturerQuery);

while ($lecturer = mysqli_fetch_assoc($lecturerResult)) {
    // 2. В value теперь ОБЯЗАТЕЛЬНО будет user_id
    echo "<option value='{$lecturer['user_id']}'>" . htmlspecialchars($lecturer['name'] . ' ' . $lecturer['surname']) . "</option>";
}

?>

</select>

<small class="text-muted">Hold Ctrl (Cmd on Mac) to select multiple lecturers.</small>

</div>



<button type="submit" name="assign_lecturers" class="btn btn-success w-100">

Assign Lecturer(s)

</button>

</form>

</div>

</div>



<!-- BACK TO ADMIN DASHBOARD -->

<div class="d-flex justify-content-end mt-3">

<a href="admin-dashboard.php" class="btn btn-primary">

Back to Admin Control Panel

</a>

</div>



</div> <!-- END RIGHT COLUMN -->



</div> <!-- END ROW -->

</div> <!-- END CONTAINER -->



<?php include "includes/footer.php"; ?>