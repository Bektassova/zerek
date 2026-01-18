<?php
include "includes/header.php";
require_once "includes/dbh.php";

// Admin only
if (!isset($_SESSION["userId"]) || ($_SESSION["role"] ?? "") !== "Admin") {
    header("Location: login.php");
    exit();
}

// --- Fetch courses for dropdown ---
$courses = [];
$cq = mysqli_query($conn, "SELECT course_id, course_name FROM courses ORDER BY course_name ASC");
if ($cq) {
    $courses = mysqli_fetch_all($cq, MYSQLI_ASSOC);
}

/**
 * ----------------------------
 * MOVE TIMETABLE ENTRIES (NEW)
 * ----------------------------
 * This allows admin to reassign existing timetable entries
 * from one course to another (e.g., Business Studies -> Interaction Design).
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['move_timetable'])) {

    $fromCourseId = (int)($_POST['from_course_id'] ?? 0);
    $toCourseId   = (int)($_POST['to_course_id'] ?? 0);

    if ($toCourseId <= 0) {
        $_SESSION['flash_error'] = "Please select the target course.";
        header("Location: admin-timetable.php");
        exit();
    }

    // -1 means: move orphaned entries (entries whose course was deleted)
    if ($fromCourseId === -1) {
        $sql = "
            UPDATE timetable t
            LEFT JOIN courses c ON c.course_id = t.course_id
            SET t.course_id = ?
            WHERE c.course_id IS NULL
        ";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $toCourseId);
        mysqli_stmt_execute($stmt);
        $affected = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['flash_success'] = "Moved {$affected} orphaned timetable entries to the selected course.";
        header("Location: admin-timetable.php");
        exit();
    }

    if ($fromCourseId <= 0) {
        $_SESSION['flash_error'] = "Please select the source course.";
        header("Location: admin-timetable.php");
        exit();
    }

    $sql = "UPDATE timetable SET course_id = ? WHERE course_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $toCourseId, $fromCourseId);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    $_SESSION['flash_success'] = "Moved {$affected} timetable entries to the selected course.";
    header("Location: admin-timetable.php");
    exit();
}

// --- Source courses that currently exist in timetable entries (NEW) ---
$sourceCourses = [];
$scq = mysqli_query($conn, "
    SELECT DISTINCT c.course_id, c.course_name
    FROM timetable t
    JOIN courses c ON c.course_id = t.course_id
    ORDER BY c.course_name
");
if ($scq) {
    $sourceCourses = mysqli_fetch_all($scq, MYSQLI_ASSOC);
}

// --- Check if there are orphaned timetable entries (NEW) ---
$hasOrphans = false;
$oq = mysqli_query($conn, "
    SELECT 1
    FROM timetable t
    LEFT JOIN courses c ON c.course_id = t.course_id
    WHERE c.course_id IS NULL
    LIMIT 1
");
if ($oq && mysqli_num_rows($oq) > 0) {
    $hasOrphans = true;
}

// --- Filter preview by selected course (optional) ---
$selectedCourseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// --- Preview timetable rows ---
$previewSql = "
SELECT t.*, c.course_name
FROM timetable t
LEFT JOIN courses c ON c.course_id = t.course_id
";

if ($selectedCourseId > 0) {
    $previewSql .= " WHERE t.course_id = " . (int)$selectedCourseId . " ";
}

$previewSql .= "
 ORDER BY FIELD(class_day,'Monday','Tuesday','Wednesday','Thursday','Friday'),
 start_time ASC
";

$previewResult = mysqli_query($conn, $previewSql);
?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">📅 Timetable Builder (Admin)</h2>
        <a href="admin-dashboard.php" class="btn btn-outline-secondary btn-sm">Back to Admin Panel</a>
    </div>

    <!-- Flash messages -->
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

    <!-- ========================= -->
    <!-- Move Timetable (NEW UI)   -->
    <!-- ========================= -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Move timetable entries to another course</h5>

            <form method="post" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-bold">From (source course)</label>
                    <select name="from_course_id" class="form-select">
                        <option value="0">-- Select source course --</option>

                        <?php if ($hasOrphans): ?>
                            <option value="-1">Orphaned entries (deleted course)</option>
                        <?php endif; ?>

                        <?php foreach ($sourceCourses as $sc): ?>
                            <option value="<?php echo (int)$sc['course_id']; ?>">
                                <?php echo htmlspecialchars($sc['course_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">
                        Select which course currently owns the timetable entries.
                    </div>
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-bold">To (target course)</label>
                    <select name="to_course_id" class="form-select" required>
                        <option value="0">-- Select target course --</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?php echo (int)$c['course_id']; ?>">
                                <?php echo htmlspecialchars($c['course_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">
                        Entries will be reassigned to this course.
                    </div>
                </div>

                <div class="col-md-2 d-grid">
                    <button type="submit" name="move_timetable" class="btn btn-warning"
                            onclick="return confirm('Move all timetable entries to the selected course?');">
                        Move Timetable
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Builder form -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="post" action="includes/admin-timetable-inc.php" class="row g-3">

                <div class="col-md-3">
                    <label class="form-label fw-bold">Course</label>
                    <select name="course_id" class="form-select" required>
                        <option value="">-- Select course --</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?php echo (int)$c['course_id']; ?>">
                                <?php echo htmlspecialchars($c['course_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Day</label>
                    <select name="class_day" class="form-select" required>
                        <option value="">-- Select day --</option>
                        <option>Monday</option>
                        <option>Tuesday</option>
                        <option>Wednesday</option>
                        <option>Thursday</option>
                        <option>Friday</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Start Time</label>
                    <input type="time" name="start_time" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">End Time</label>
                    <input type="time" name="end_time" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Subject</label>
                    <input type="text" name="subject_name" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Room</label>
                    <input type="text" name="room_number" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Type</label>
                    <select name="class_type" class="form-select" required>
                        <option value="">-- Select type --</option>
                        <option>Lecture</option>
                        <option>Seminar</option>
                        <option>Workshop</option>
                        <option>Practical</option>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" name="add_timetable" class="btn btn-success">
                        Add to Timetable
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Preview table -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <span>Current Timetable Entries</span>

            <form method="get" class="d-flex gap-2 align-items-center">
                <select name="course_id" class="form-select form-select-sm" style="width: 220px;">
                    <option value="0">All courses</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?php echo (int)$c['course_id']; ?>"
                            <?php echo ($selectedCourseId == (int)$c['course_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-outline-light btn-sm" type="submit">Filter</button>
                <a class="btn btn-outline-light btn-sm" href="admin-timetable.php">Reset</a>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Course</th>
                        <th>Day</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Subject</th>
                        <th>Room</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($previewResult && mysqli_num_rows($previewResult) > 0): ?>
                    <?php while ($r = mysqli_fetch_assoc($previewResult)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['course_name'] ?? '—'); ?></td>
                            <td><?php echo htmlspecialchars($r['class_day']); ?></td>
                            <td><?php echo htmlspecialchars(substr($r['start_time'], 0, 5)); ?></td>
                            <td><?php echo htmlspecialchars(substr($r['end_time'], 0, 5)); ?></td>
                            <td><?php echo htmlspecialchars($r['subject_name']); ?></td>
                            <td><?php echo htmlspecialchars($r['room_number']); ?></td>
                            <td><?php echo htmlspecialchars($r['class_type']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No timetable entries yet.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include "includes/footer.php"; ?>
