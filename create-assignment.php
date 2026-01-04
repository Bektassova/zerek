<?php
session_start();
require_once "includes/dbh.php";

// SECURITY: only Lecturer can access this page
if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Lecturer") {
    header("location: login.php");
    exit();
}

// Get the logged-in user's ID (this is the "Passport ID" like 25 for Alice)
$lecturerUserId = $_SESSION["userId"];

/**
 * 1. Fetch units assigned specifically to this lecturer.
 * We now use the user_id directly because our 'lecturer_units' table 
 * uses the main system ID from the 'users' table.
 */
$sqlUnits = "
    SELECT u.unit_id, u.unit_name
    FROM units u
    INNER JOIN lecturer_units lu ON u.unit_id = lu.unit_id
    WHERE lu.lecturer_id = ?
    ORDER BY u.unit_name ASC
";

$stmt = mysqli_prepare($conn, $sqlUnits);
mysqli_stmt_bind_param($stmt, "i", $lecturerUserId);
mysqli_stmt_execute($stmt);
$unitsResult = mysqli_stmt_get_result($stmt);

// Note: We removed the old step that was looking for the 'lecturer_id' 
// from the 'lecturers' table to avoid foreign key mismatch.
?>

<?php require_once "includes/header.php"; ?>

<div class="container mt-5">
    <h2 class="mb-4">Create Assignment</h2>

    <div class="card shadow">
        <div class="card-body">

            <form action="includes/create-assignment-inc.php" method="post" enctype="multipart/form-data">

                <!-- UNIT -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Unit</label>
                    <select name="unit_id" class="form-select" required>
                        <option value="">-- Select Unit --</option>
                        <?php while ($unit = mysqli_fetch_assoc($unitsResult)): ?>
                            <option value="<?php echo $unit['unit_id']; ?>">
                                <?php echo htmlspecialchars($unit['unit_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- TITLE -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Assignment Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <!-- DESCRIPTION -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea name="description" class="form-control" rows="4"></textarea>
                </div>

                <!-- DUE DATE -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Due Date</label>
                    <input type="date" name="due_date" class="form-control">
                </div>

                <!-- FILE -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Assignment File</label>
                    <input type="file" name="assignment_file" class="form-control">
                    <small class="text-muted">
                        Allowed: PDF, DOC, DOCX
                    </small>
                </div>

                <button type="submit" name="create_assignment" class="btn btn-success">
                    Create Assignment
                </button>

                <a href="lecturer-assignments.php" class="btn btn-secondary ms-2">
                    Cancel
                </a>

            </form>

        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>
