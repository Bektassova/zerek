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
    sf.file_id,
    sf.file_path,
    sf.upload_date
FROM submissions s
JOIN assignments a ON a.assignment_id = s.assignment_id
JOIN units u ON u.unit_id = a.unit_id
JOIN lecturer_units lu ON lu.unit_id = u.unit_id
JOIN users st ON st.user_id = s.student_id
LEFT JOIN submission_files sf ON sf.submission_id = s.submission_id
WHERE lu.lecturer_id = ?
ORDER BY s.submission_date DESC, sf.upload_date DESC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $lecturerId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Small helper: build correct download URL
function buildFileUrl($path) {
    if (!$path) return null;

    // If DB stores just filename, we assume uploads/submissions/
    if (strpos($path, "uploads/") === false) {
        return "uploads/submissions/" . ltrim($path, "/");
    }
    // If DB stores full relative path like uploads/submissions/file.ext
    return ltrim($path, "/");
}
?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Submissions (Lecturer View)</h2>
        <a href="profile.php" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

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
                            <th>File</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($result) === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No submissions yet for your units.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <?php
                                $fileUrl = buildFileUrl($row["file_path"] ?? null);
                                $fileName = $row["file_path"] ? basename($row["file_path"]) : null;
                            ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($row["submission_date"] ?? ""); ?>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        <?php echo htmlspecialchars($row["unit_name"] ?? ""); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($row["assignment_title"] ?? ""); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars(($row["name"] ?? "") . " " . ($row["surname"] ?? "")); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($row["email"] ?? ""); ?>
                                </td>
                                <td class="text-nowrap">
                                    <?php if ($fileUrl): ?>
                                        <a class="btn btn-sm btn-outline-primary"
                                           href="<?php echo htmlspecialchars($fileUrl); ?>"
                                           target="_blank">
                                            Download
                                        </a>
                                        <div class="small text-muted mt-1">
                                            <?php echo htmlspecialchars($fileName); ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">No file</span>
                                    <?php endif; ?>
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
