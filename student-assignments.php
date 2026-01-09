<?php
include "includes/header.php";
require_once "includes/dbh.php";

// Security: only logged-in students
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Student') {
    header("Location: login.php");
    exit();
}

$studentId = (int) $_SESSION['userId'];

/*
 Show assignments ONLY for units where the student is enrolled
 + attach submission status and grade
*/
$sql = "
SELECT 
    a.assignment_id,
    a.title,
    a.description,
    a.due_date,
    a.file_path,
    u.unit_name,

    s.submission_id,
    s.submission_date,

    g.mark,
    g.feedback,
    g.graded_at
FROM assignments a
JOIN units u ON a.unit_id = u.unit_id
JOIN student_units su ON su.unit_id = u.unit_id

LEFT JOIN submissions s
  ON s.assignment_id = a.assignment_id
 AND s.student_id = ?
 AND s.submission_id = (
      SELECT MAX(s2.submission_id)
      FROM submissions s2
      WHERE s2.assignment_id = a.assignment_id
        AND s2.student_id = ?
 )

LEFT JOIN grades g ON g.submission_id = s.submission_id

WHERE su.student_id = ?
ORDER BY a.due_date ASC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iii", $studentId, $studentId, $studentId);
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
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Unit</th>
                    <th>Title</th>
                    <th>Due Date</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Grade</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['unit_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['due_date']); ?></td>

                        <td>
                            <?php if (!empty($row['file_path'])): ?>
                                <a href="uploads/<?php echo rawurlencode($row['file_path']); ?>"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    Download
                                </a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>

                        <!-- STATUS -->
                        <td>
                            <?php if (!empty($row['submission_id'])): ?>
                                <span class="badge bg-success">Submitted</span>
                                <div class="small text-muted"><?php echo htmlspecialchars($row['submission_date']); ?></div>
                            <?php else: ?>
                                <span class="badge bg-secondary">Not submitted</span>
                            <?php endif; ?>
                        </td>

                        <!-- GRADE -->
                        <td>
                            <?php if (!is_null($row['mark'])): ?>
                                <span class="badge bg-primary"><?php echo (int)$row['mark']; ?>/100</span>
                                <?php if (!empty($row['feedback'])): ?>
                                    <div class="small text-muted mt-1">
                                        <?php echo htmlspecialchars($row['feedback']); ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">Not graded</span>
                            <?php endif; ?>
                        </td>

                        <!-- ACTION -->
                        <td class="text-nowrap">
                            <a href="student-submit-assignment.php?assignment_id=<?php echo (int)$row['assignment_id']; ?>"
                               class="btn btn-sm <?php echo !empty($row['submission_id']) ? 'btn-outline-success' : 'btn-success'; ?>">
                               <?php echo !empty($row['submission_id']) ? 'Resubmit' : 'Submit'; ?>
                            </a>
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
