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
 IMPORTANT ASSUMPTION (based on your system):
 - assignments.lecturer_id stores users.user_id (NOT lecturers table)
*/

// Fetch assignments created by this lecturer
$sql = "
    SELECT 
        a.assignment_id,
        a.title,
        a.description,
        a.due_date,
        a.file_path,
        u.unit_name,
        a.created_at
    FROM assignments a
    JOIN units u ON a.unit_id = u.unit_id
    WHERE a.lecturer_id = ?
    ORDER BY a.created_at DESC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $lecturerUserId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<?php require_once "includes/header.php"; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Assignments</h2>
        <a href="create-assignment.php" class="btn btn-primary">
            + Create Assignment
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Unit</th>
                        <th>Due Date</th>
                        <th>File</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>

                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>

                            <td><?php echo htmlspecialchars($row['unit_name']); ?></td>

                            <td>
                                <?php echo $row['due_date'] ? htmlspecialchars($row['due_date']) : '—'; ?>
                            </td>

                            <td>
                                <?php if ($row['file_path']): ?>
                                    <a href="<?php echo htmlspecialchars($row['file_path']); ?>" target="_blank">
                                        View File
                                    </a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php echo date("Y-m-d", strtotime($row['created_at'])); ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No assignments created yet.
                        </td>
                    </tr>
                <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>
