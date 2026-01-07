<?php 
    include "includes/header.php";
    require_once 'includes/dbh.php';

    if (!isset($_SESSION["userId"])) {
        header("location: login.php");
        exit();
    }

    $userId = $_SESSION["userId"];

    // Fetch timetable for this specific user
    $sql = "SELECT * FROM timetable WHERE user_id = ? 
            ORDER BY FIELD(class_day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'), start_time ASC;";
    $stmt = mysqli_stmt_init($conn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-calendar-alt text-primary"></i> My Weekly Schedule</h2>
        <a href="profile.php" class="btn btn-outline-secondary">Back to Profile</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>Day</th>
                        <th>Subject</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <!-- День недели -->
                                <td class="fw-bold"><?php echo $row['class_day']; ?></td>

                                <!-- Предмет -->
                                <td><?php echo htmlspecialchars($row['subject_name']); ?></td>

                                <!-- Время -->
                                <td>
                                    <?php 
                                        echo date("H:i", strtotime($row['start_time'])) 
                                             . " - " 
                                             . date("H:i", strtotime($row['end_time']));
                                    ?>
                                </td>

                                <!-- Аудитория / Онлайн -->
                                <td>
                                    <?php 
                                        $room = htmlspecialchars($row['room_number']); 
                                        if (strtolower($room) === 'online') {
                                            echo '<span class="badge bg-success text-white">' . $room . '</span>';
                                        } else {
                                            echo '<span class="badge bg-light text-dark border">' . $room . '</span>';
                                        }
                                    ?>
                                </td>

                                <!-- Тип занятия -->
                                <td><?php echo htmlspecialchars($row['class_type']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No classes scheduled yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
