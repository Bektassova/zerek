<?php
session_start();
require_once "includes/dbh.php";

// SECURITY: only Lecturer
if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Lecturer") {
    header("location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Assignment ID not provided.");
}

$assignmentId = (int)$_GET['id'];
$lecturerUserId = $_SESSION["userId"];

// Get lecturer_id
$sqlLecturer = "SELECT lecturer_id FROM lecturers WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sqlLecturer);
mysqli_stmt_bind_param($stmt, "i", $lecturerUserId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$lecturer = mysqli_fetch_assoc($result);

if (!$lecturer) {
    die("Lecturer record not found.");
}
$lecturerId = $lecturer['lecturer_id'];

// Get assignment
$sqlAssignment = "
    SELECT a.*, u.unit_name, u.unit_id
    FROM assignments a
    JOIN units u ON a.unit_id = u.unit_id
    INNER JOIN lecturer_units lu ON u.unit_id = lu.unit_id
    WHERE a.assignment_id = ? AND lu.lecturer_id = ?
";
$stmt = mysqli_prepare($conn, $sqlAssignment);
mysqli_stmt_bind_param($stmt, "ii", $assignmentId, $lecturerId);
mysqli_stmt_execute($stmt);
$assignmentResult = mysqli_stmt_get_result($stmt);
$assignment = mysqli_fetch_assoc($assignmentResult);

if (!$assignment) {
    die("Assignment not found or you do not have access.");
}

// Get all units for this lecturer
$sqlUnits = "
    SELECT u.unit_id, u.unit_name
    FROM units u
    INNER JOIN lecturer_units lu ON u.unit_id = lu.unit_id
    WHERE lu.lecturer_id = ?
    ORDER BY u.unit_name ASC
";
$stmt = mysqli_prepare($conn, $sqlUnits);
mysqli_stmt_bind_param($stmt, "i", $lecturerId);
mysqli_stmt_execute($stmt);
$unitsResult = mysqli_stmt_get_result($stmt);
?>

<?php require_once "includes/header.php"; ?>

<div class="container mt-5">
    <h2 class="mb-4">Edit Assignment</h2>
    <div class="card shadow">
        <div class="card-body">
            <form action="includes/edit-assignment-inc.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="assignment_id" value="<?php echo $assignment['assignment_id']; ?>">

                <!-- Unit -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Unit</label>
                    <select name="unit_id" class="form-select" required>
                        <?php while ($unit = mysqli_fetch_assoc($unitsResult)): ?>
                            <option value="<?php echo $unit['unit_id']; ?>" <?php if($unit['unit_id']==$assignment['unit_id']) echo "selected"; ?>>
                                <?php echo htmlspecialchars($unit['unit_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Title -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Assignment Title</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($assignment['title']); ?>" required>
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($assignment['description']); ?></textarea>
                </div>

                <!-- Due Date -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Due Date</label>
                    <input type="date" name="due_date" class="form-control" value="<?php echo $assignment['due_date']; ?>">
                </div>

                <!-- File -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Assignment File</label>
                    <input type="file" name="assignment_file" class="form-control">
                    <?php if(!empty($assignment['file_path'])): ?>
                        <small class="text-muted">Current file: <?php echo htmlspecialchars($assignment['file_path']); ?></small>
                    <?php endif; ?>
                </div>

                <button type="submit" name="update_assignment" class="btn btn-success">Update Assignment</button>
                <a href="lecturer-assignments.php" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
