<?php
include "includes/header.php";
require_once "includes/dbh.php";

// Security: only logged-in students
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Student') {
    header("Location: login.php");
    exit();
}

$studentId = $_SESSION['userId'];

/*
 We show assignments ONLY for units
 where the student is enrolled
*/
$sql = "
    SELECT 
        a.title,
        a.description,
        a.due_date,
        a.file_path,
        u.unit_name
    FROM assignments a
    JOIN units u ON a.unit_id = u.unit_id
    JOIN student_units su ON su.unit_id = u.unit_id
    WHERE su.student_id = ?
    ORDER BY a.due_date ASC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $studentId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="container mt-5">
    <h2 class="mb-4">My Assignments</h2>

    <?php if (mysqli_num_rows($result) === 0): ?>
        <div class="alert alert-info">
            No assignments available yet.
        </div>
    <?php else: ?>
        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Unit</th>
                    <th>Title</th>
                    <th>Due Date</th>
                    <th>File</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['unit_name']) ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['due_date']) ?></td>
                        <td>
                            <?php if ($row['file_path']): ?>
                               <a href="uploads/<?php echo htmlspecialchars($row['file_path']); ?>" 
   target="_blank"
   class="btn btn-sm btn-outline-primary">
   Download
</a>

                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="profile.php" class="btn btn-secondary mt-3">
        Back to Profile
    </a>
</div>

<?php include "includes/footer.php"; ?>
