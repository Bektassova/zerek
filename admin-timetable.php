<?php
include "includes/header.php";
require_once "includes/dbh.php";

// Admin only
if (!isset($_SESSION["userId"]) || ($_SESSION["role"] ?? "") !== "Admin") {
    header("Location: login.php");
    exit();
}

// --- Fetch courses for dropdown (if you have courses table) ---
$courses = [];
$cq = mysqli_query($conn, "SELECT course_id, course_name FROM courses ORDER BY course_name ASC");
if ($cq) {
    $courses = mysqli_fetch_all($cq, MYSQLI_ASSOC);
}

// --- Filter preview by selected course (optional) ---
// --- Filter preview by selected course (optional) ---
$selectedCourseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// --- Preview timetable rows (БЕЗ JOIN — работает с твоей текущей структурой) ---
$previewSql = "
SELECT t.*, c.course_name
FROM timetable t
LEFT JOIN courses c 
       ON c.course_id = t.course_id
";


if ($selectedCourseId > 0) {
    // ВРЕМЕННО: пока мы не знаем точное имя колонки, фильтр отключаем
    // (иначе снова будет ошибка)
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
                        <td colspan="6" class="text-center text-muted py-4">No timetable entries yet.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include "includes/footer.php"; ?>
