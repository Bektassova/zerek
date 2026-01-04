<?php
session_start();
require_once "includes/dbh.php";

// SECURITY: only Lecturer
if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Lecturer") {
    header("location: login.php");
    exit();
}

$lecturerUserId = $_SESSION["userId"];

/*
 Fetch ONLY units assigned to this lecturer
 (uses lecturer_units table)
*/
$sql = "
    SELECT u.unit_id, u.unit_name
    FROM units u
    JOIN lecturer_units lu ON u.unit_id = lu.unit_id
    WHERE lu.lecturer_id = ?
    ORDER BY u.unit_name ASC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $lecturerUserId);
mysqli_stmt_execute($stmt);
$unitsResult = mysqli_stmt_get_result($stmt);
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
