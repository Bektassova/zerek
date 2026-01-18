<?php
session_start();
require_once "includes/dbh.php";

/*
|--------------------------------------------------------------------------
| Student Timetable (View Only)
|--------------------------------------------------------------------------
| Displays timetable entries for:
| - the student's course (course_id)
| - plus any optional personal entries (user_id), if they exist
|--------------------------------------------------------------------------
*/

// Security: any logged-in user can view their timetable
if (!isset($_SESSION["userId"])) {
    header("Location: login.php");
    exit();
}

$userId = (int) $_SESSION["userId"];

/*
|--------------------------------------------------------------------------
| 1) Get student's course_id
|--------------------------------------------------------------------------
*/
$courseId = 0;

$sqlCourse = "SELECT course_id FROM users WHERE user_id = ?";
$stmtCourse = mysqli_prepare($conn, $sqlCourse);
mysqli_stmt_bind_param($stmtCourse, "i", $userId);
mysqli_stmt_execute($stmtCourse);
$resCourse = mysqli_stmt_get_result($stmtCourse);

if ($resCourse && ($courseRow = mysqli_fetch_assoc($resCourse))) {
    $courseId = (int) ($courseRow["course_id"] ?? 0);
}

mysqli_stmt_close($stmtCourse);

/*
|--------------------------------------------------------------------------
| 2) Fetch timetable entries (course-based + optional personal)
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT *
    FROM timetable
    WHERE (course_id = ? OR user_id = ?)
    ORDER BY FIELD(class_day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'),
             start_time ASC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $courseId, $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

include "includes/header.php";
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-calendar-alt text-primary"></i> My Timetable</h2>
        <a href="profile.php" class="btn btn-outline-secondary">Back to Profile</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Day</th>
                        <th>Subject</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Room</th>
                        <th>Type</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <?php
                                // Format times safely
                                $start = !empty($row['start_time']) ? date("H:i", strtotime($row['start_time'])) : "--:--";
                                $end   = !empty($row['end_time'])   ? date("H:i", strtotime($row['end_time']))   : "--:--";

                                // Room display (badge for "online")
                                $roomRaw = (string)($row['room_number'] ?? '');
                                $roomSafe = htmlspecialchars($roomRaw);
                                $isOnline = (strtolower(trim($roomRaw)) === 'online');
                            ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($row['class_day'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row['subject_name'] ?? ''); ?></td>
                                <td><?php echo $start; ?></td>
                                <td><?php echo $end; ?></td>
                                <td>
                                    <?php if ($roomSafe === ''): ?>
                                        <span class="text-muted">—</span>
                                    <?php elseif ($isOnline): ?>
                                        <span class="badge bg-success text-white"><?php echo $roomSafe; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border"><?php echo $roomSafe; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['class_type'] ?? ''); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No classes scheduled yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>

