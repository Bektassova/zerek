<?php
session_start();
require_once 'includes/dbh.php';

// Security check: only lecturers can access
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Lecturer') {
    header("Location: login.php");
    exit();
}

$lecturerId = $_SESSION["lecturer_id"];

// Fetch assignments created by this lecturer
$sql = "
    SELECT a.*, u.unit_name
    FROM assignments a
    JOIN units u ON a.unit_id = u.unit_id
    WHERE a.lecturer_id = ?
    ORDER BY a.created_at DESC
";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $lecturerId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="container mt-5">
    <h2>My Assignments</h2>
    <a href="student.assign.php" class="btn btn-primary mb-3">Create New Assignment</a>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Title</th>
                <th>Unit</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($a = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($a['title']) ?></td>
                    <td><?= htmlspecialchars($a['unit_name']) ?></td>
                    <td><?= htmlspecialchars($a['due_date']) ?></td>
                    <td>
                        <a href="student.assign.php?id=<?= $a['assignment_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="includes/assignments-inc.php?delete=<?= $a['assignment_id'] ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Delete this assignment?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4" class="text-center">No assignments found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
