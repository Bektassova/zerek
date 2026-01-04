<?php
session_start();
require_once 'includes/dbh.php';

if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Lecturer') {
    header("Location: login.php");
    exit();
}

$lecturerId = $_SESSION['userId'];

// Fetch units assigned to this lecturer
$sqlUnits = "SELECT u.unit_id, u.unit_name 
             FROM units u
             JOIN lecturer_units lu ON u.unit_id = lu.unit_id
             WHERE lu.lecturer_id = ?";
$stmt = mysqli_prepare($conn, $sqlUnits);
mysqli_stmt_bind_param($stmt, "i", $lecturerId);
mysqli_stmt_execute($stmt);
$unitsResult = mysqli_stmt_get_result($stmt);

// If editing
$assignment = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM assignments WHERE assignment_id=? AND lecturer_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $id, $lecturerId);
    mysqli_stmt_execute($stmt);
    $assignment = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}
?>

<div class="container mt-5">
    <h2><?= $assignment ? "Edit Assignment" : "Create Assignment" ?></h2>

    <form action="includes/assignments-inc.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="assignment_id" value="<?= $assignment['assignment_id'] ?? '' ?>">

        <label for="unit_id">Select Unit</label>
        <select name="unit_id" required>
            <option value="">-- Select Unit --</option>
            <?php while ($unit = mysqli_fetch_assoc($unitsResult)): ?>
                <option value="<?= $unit['unit_id'] ?>" 
                    <?= ($assignment && $assignment['unit_id'] == $unit['unit_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($unit['unit_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="title">Assignment Title</label>
        <input type="text" name="title" value="<?= $assignment['title'] ?? '' ?>" required>

        <label for="description">Description</label>
        <textarea name="description"><?= $assignment['description'] ?? '' ?></textarea>

        <label for="file">Upload File</label>
        <input type="file" name="file">

        <label for="due_date">Due Date</label>
        <input type="date" name="due_date" value="<?= $assignment['due_date'] ?? '' ?>">

        <button type="submit" name="create_assignment"><?= $assignment ? "Save Changes" : "Create Assignment" ?></button>
    </form>
</div>
