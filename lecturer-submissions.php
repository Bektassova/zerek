<?php
include "includes/header.php";
require_once "includes/dbh.php";

// Security: only Lecturer
if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Lecturer") {
    header("Location: login.php");
    exit();
}

$lecturerId = (int) $_SESSION["userId"];

/*
Shows submissions for assignments that belong to units assigned to this lecturer.
Tables used:
- lecturer_units (lecturer_id, unit_id)
- assignments (assignment_id, unit_id, title, ...)
- submissions (submission_id, assignment_id, student_id, submission_date)
- submission_files (file_id, submission_id, file_path, upload_date)
- grades (grade_id, submission_id, lecturer_id, mark, feedback, graded_at)
- users (student info)
- units (unit_name)
*/

$sql = "
SELECT
    s.submission_id,
    s.submission_date,
    a.assignment_id,
    a.title AS assignment_title,
    u.unit_name,
    st.user_id AS student_id,
    st.name,
    st.surname,
    st.email,

    g.grade_id,
    g.mark,
    g.feedback,
    g.graded_at,

    GROUP_CONCAT(sf.file_path ORDER BY sf.upload_date DESC SEPARATOR '||') AS file_paths
FROM submissions s
JOIN assignments a ON a.assignment_id = s.assignment_id
JOIN units u ON u.unit_id = a.unit_id
JOIN lecturer_units lu ON lu.unit_id = u.unit_id
JOIN users st ON st.user_id = s.student_id
LEFT JOIN submission_files sf ON sf.submission_id = s.submission_id
LEFT JOIN grades g ON g.submission_id = s.submission_id
WHERE lu.lecturer_id = ?
GROUP BY
    s.submission_id, s.submission_date,
    a.assignment_id, a.title,
    u.unit_name,
    st.user_id, st.name, st.surname, st.email,
    g.grade_id, g.mark, g.feedback, g.graded_at
ORDER BY s.submission_date DESC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $lecturerId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Helper: build correct download URL
function buildFileUrl($path) {
    if (!$path) return null;

    if (strpos($path, "uploads/") === false) {
        return "uploads/submissions/" . ltrim($path, "/");
    }
    return ltrim($path, "/");
}
?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Submissions (Lecturer View)</h2>
        <a href="profile.php" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['flash_success']); ?>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($_SESSION['flash_error']); ?>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Unit</th>
                            <th>Assignment</th>
                            <th>Student</th>
                            <th>Email</th>
                            <th>Files</th>
                            <th style="min-width:160px;">Mark (0–100)</th>
                            <th style="min-width:260px;">Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($result) === 0): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No submissions yet for your units.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row["submission_date"] ?? ""); ?></td>

                                <td>
                                    <span class="badge bg-info text-dark">
                                        <?php echo htmlspecialchars($row["unit_name"] ?? ""); ?>
                                    </span>
                                </td>

                                <td><?php echo htmlspecialchars($row["assignment_title"] ?? ""); ?></td>

                                <td><?php echo htmlspecialchars(($row["name"] ?? "") . " " . ($row["surname"] ?? "")); ?></td>

                                <td><?php echo htmlspecialchars($row["email"] ?? ""); ?></td>

                                <td class="text-nowrap">
                                    <?php
                                        $paths = [];
                                        if (!empty($row["file_paths"])) {
                                            $paths = explode("||", $row["file_paths"]);
                                        }
                                    ?>
                                    <?php if (!empty($paths)): ?>
                                        <?php foreach ($paths as $p): ?>
                                            <?php $fileUrl = buildFileUrl($p); ?>
                                            <div class="mb-1">
                                               <a class="btn btn-sm btn-outline-primary"
   href="<?php echo $BASE_PATH . htmlspecialchars($fileUrl); ?>"
   target="_blank">Download</a>


                                                <span class="small text-muted ms-2"><?php echo htmlspecialchars(basename($p)); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">No file</span>
                                    <?php endif; ?>
                                </td>

                                <!-- One grading form (mark + feedback together) -->
                              <td style="min-width:320px;">
<form method="post" action="includes/grade-submission-inc.php">

    <input type="hidden" name="submission_id" value="<?php echo (int)$row['submission_id']; ?>">

    <div class="d-flex gap-2 mb-2">
        <input type="number"
               name="mark"
               min="0" max="100"
               class="form-control form-control-sm"
               style="max-width:80px"
               value="<?php echo (int)($row['mark'] ?? ''); ?>"
               placeholder="0-100">

        <textarea name="feedback"
                  class="form-control form-control-sm"
                  rows="2"
                  placeholder="Feedback..."><?php echo htmlspecialchars($row['feedback'] ?? ''); ?></textarea>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" name="save_grade" class="btn btn-sm btn-success">Save</button>

        <form method="post" action="includes/delete-submission-inc.php"
              onsubmit="return confirm('Delete this submission permanently?');">
            <input type="hidden" name="submission_id" value="<?php echo (int)$row['submission_id']; ?>">
            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
        </form>
    </div>

    <div class="small text-muted mt-1">
        <?php echo !empty($row['graded_at']) ? "Graded: ".$row['graded_at'] : "Not graded yet"; ?>
    </div>

</form>
</td>

                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
