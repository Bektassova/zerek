<?php 
    include "includes/header.php";
    require_once 'includes/dbh.php';
    require_once 'includes/functions.php';

    if (!isset($_SESSION["userId"])) {
        header("location: login.php?error=notloggedin");
        exit();
    }

    $userId = $_SESSION["userId"];
    $user = getUser($conn, $userId); 

    if (!$user) {
        echo "<p>Error: Could not load user profile.</p>";
        include "includes/footer.php";
        exit();
    }

    $currentRole = $user['role']; 
    $photo = !empty($user['profile_photo']) ? $user['profile_photo'] : 'assets/default-avatar.png';
?>

<?php 
// Определяем отступ: если Lecturer — делаем больше (mt-5 + pt-5), если Student — оставляем стандарт (mt-4)
$topMarginClass = ($_SESSION['role'] === 'Lecturer') ? 'mt-5 pt-5' : 'mt-4'; 
?>

<div class="container-fluid <?php echo $topMarginClass; ?>" style="max-width: 1450px; padding-left: 20px;">
    
<!-- ЭТОТ БЛОК ОБЩИЙ: И для студента, и для преподавателя -->
    <div class="row mb-5 align-items-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-header bg-primary text-white py-1">
                    <h5 class="mb-0 small fw-bold">Personal Information</h5>
                </div>
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center border-end">
                            <img src="<?php echo htmlspecialchars($photo); ?>" class="rounded-circle mb-2" width="100" height="100" style="object-fit:cover; border: 3px solid #0d6efd;">
                            <form action="includes/upload-profile-photo.php" method="POST" enctype="multipart/form-data" class="mt-1">
                                <input type="file" name="photo" class="form-control form-control-sm mb-1" style="font-size: 0.6rem;">
                                <button class="btn btn-primary btn-sm w-100" style="font-size: 0.6rem;">Update Photo</button>
                            </form>
                        </div>
                        <div class="col-md-8 px-4" style="font-size: 0.9rem;">
                            <p class="mb-1"><strong>Full Name:</strong> <?php echo htmlspecialchars($user['name']." ".$user['surname']); ?></p>
                            <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                            <p class="mb-1"><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                            <p class="mb-0"><strong>Role:</strong> <span class="badge bg-info text-dark"><?php echo $currentRole; ?></span></p>
                            <a href="edit-profile.php" class="btn btn-link btn-sm p-0 mt-2 text-decoration-none">Edit Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 d-none d-md-block text-center">
             <img src="https://cache.careers360.mobi/media/article_images/2023/2/3/importance-of-education.jpg" class="img-fluid rounded shadow-sm" style="max-height: 180px;">
        </div>
    </div>
    <?php 
    // ======================== СЕКЦИЯ СТУДЕНТА ========================
    if ($currentRole === "Student"): 
        // Нам нужно загрузить юниты студента здесь
        $sqlMyUnits = "SELECT u.unit_name FROM student_units su JOIN units u ON u.unit_id = su.unit_id WHERE su.student_id = ? ORDER BY u.unit_name ASC";
        $stmtMyUnits = mysqli_prepare($conn, $sqlMyUnits);
        mysqli_stmt_bind_param($stmtMyUnits, "i", $userId);
        mysqli_stmt_execute($stmtMyUnits);
        $resultMyUnits = mysqli_stmt_get_result($stmtMyUnits);
    ?>
    
    <div class="row g-4">
        <!-- КОЛОНКА 1: DASHBOARD -->
        <div class="col-md-4">
            <h3 class="mb-4 text-dark fw-bold small text-uppercase" style="letter-spacing: 1px;">
                <i class="fas fa-th-large me-2 text-primary"></i>Dashboard
            </h3>
            <div class="row g-3">
                <div class="col-12">
                    <div class="card border-start border-dark border-4 shadow-sm">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-1">My Units</h6>
                            <div class="card-body p-3">



    <a
        href="student-units.php"
        class="btn btn-sm btn-outline-primary py-0 px-3"
        style="font-size: 0.7rem;"
    >
        View
    </a>

</div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card border-start border-primary border-4 shadow-sm">
                        <div class="card-body p-3">
                            <h6 class="text-primary fw-bold mb-1">My Timetable</h6>
                            <a href="timetable.php" class="btn btn-sm btn-outline-primary py-0 px-3" style="font-size: 0.7rem;">View</a>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card border-start border-warning border-4 shadow-sm">
                        <div class="card-body p-3">
                            <h6 class="text-success fw-bold mb-1">Assignments</h6>
                            <a href="student-assignments.php" class="btn btn-sm btn-outline-warning py-0 px-3" style="font-size: 0.7rem;">View All</a>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card border-start border-primary border-4 shadow-sm">
                        <div class="card-body p-3">
                            <h6 class="text-primary fw-bold mb-1">Latest Grades</h6>
                            <p class="card-text text-muted small mb-2">Performance.</p>
                            <a href="grades.php" class="btn btn-sm btn-outline-primary py-0 px-3" style="font-size: 0.7rem;">Reports</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<!-- КОЛОНКА 2: PERFORMANCE & PROGRESS (ЦЕНТР) -->
<div class="col-md-4 mt-5 pt-3">
    
    <!-- Карточка приветствия и важного уведомления -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body p-3 text-center">
            <h5 class="fw-bold text-primary mb-2">Welcome to your workspace!</h5>
            <div class="p-2 border border-warning rounded bg-light">
                <p class="small text-danger fw-bold mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i> 
                    All assignments must be submitted to complete the module!
                </p>
            </div>
        </div>
    </div>

    <!-- Карточка Performance Level Indicator -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-dark text-white py-2">
            <h6 class="mb-0 small fw-bold text-center">Performance Level Indicator</h6>
        </div>
        <div class="card-body p-3">
            <p class="small text-muted text-center mb-3">Track your progress using these grade bands:</p>
            
            <!-- Ряд индикаторов -->
            <div class="d-flex flex-column gap-2">
                <div class="d-flex align-items-center p-2 rounded shadow-xs border-start border-danger border-4 bg-light">
                    <div class="bg-danger rounded-circle me-3" style="width: 12px; height: 12px;"></div>
                    <span class="small fw-bold">Minimum Pass</span>
                    <span class="ms-auto small badge bg-danger">50–61%</span>
                </div>

                <div class="d-flex align-items-center p-2 rounded shadow-xs border-start border-warning border-4 bg-light">
                    <div class="bg-warning rounded-circle me-3" style="width: 12px; height: 12px;"></div>
                    <span class="small fw-bold text-dark">Satisfactory</span>
                    <span class="ms-auto small badge bg-warning text-dark">62–79%</span>
                </div>

                <div class="d-flex align-items-center p-2 rounded shadow-xs border-start border-success border-4 bg-light">
                    <div class="bg-success rounded-circle me-3" style="width: 12px; height: 12px;"></div>
                    <span class="small fw-bold">Merit</span>
                    <span class="ms-auto small badge bg-success">80–89%</span>
                </div>

                <div class="d-flex align-items-center p-2 rounded shadow-xs border-start border-primary border-4 bg-light">
                    <div class="bg-primary rounded-circle me-3" style="width: 12px; height: 12px;"></div>
                    <span class="small fw-bold">Distinction</span>
                    <span class="ms-auto small badge bg-primary">90%+</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Кнопка настроек в самом низу -->
    <div class="text-center">
        <a href="edit-profile.php" class="btn btn-sm btn-link text-muted text-decoration-none">
            <i class="fas fa-cog"></i> Profile Settings
        </a>
    </div>
</div>

        <!-- КОЛОНКА 3: КАЛЕНДАРЬ -->
        <div class="col-md-4 mt-5 pt-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-dark text-white py-2">
                    <h6 class="mb-0 small fw-bold text-center"><i class="far fa-calendar-alt me-2"></i> Academic Calendar</h6>
                </div>
                <div class="card-body p-0">
                    <div class="p-3 text-center bg-light border-bottom">
                        <h5 class="mb-0 fw-bold"><?php echo date('F Y'); ?></h5>
                    </div>
                    <table class="table table-sm table-borderless text-center mb-0" style="font-size: 0.75rem;">
                        <thead><tr class="text-muted"><th>Mo</th><th>Tu</th><th>We</th><th>Th</th><th>Fr</th><th>Sa</th><th>Su</th></tr></thead>
                        <tbody>
                            <?php
                            $today = date('j'); $daysInMonth = date('t'); $startDay = date('N', strtotime(date('Y-m-01'))); $currentDay = 1;
                            echo "<tr>";
                            for ($i = 1; $i < 8; $i++) {
                                if ($i < $startDay) echo "<td></td>";
                                else {
                                    $style = ($currentDay == $today) ? "bg-primary text-white rounded-circle fw-bold" : "";
                                    echo "<td><div class='p-1 $style'>$currentDay</div></td>"; $currentDay++;
                                }
                            }
                            echo "</tr>";
                            while ($currentDay <= $daysInMonth) {
                                echo "<tr>";
                                for ($i = 0; $i < 7 && $currentDay <= $daysInMonth; $i++) {
                                    $style = ($currentDay == $today) ? "bg-primary text-white rounded-circle fw-bold" : "";
                                    echo "<td><div class='p-1 $style'>$currentDay</div></td>"; $currentDay++;
                                }
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="p-3 small">
                        <p class="fw-bold text-muted mb-1">Events:</p>
                        <div class="d-flex align-items-center mb-1"><div class="bg-warning me-2" style="width:8px; height:8px; border-radius:50%;"></div>Assignment Due (May 22)</div>
                        <div class="d-flex align-items-center"><div class="bg-success me-2" style="width:8px; height:8px; border-radius:50%;"></div>End of Term (June 1)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php 
    // ======================== СЕКЦИЯ ПРЕПОДАВАТЕЛЯ ========================
    elseif ($currentRole === "Lecturer" || $currentRole === "Teacher"): 
    ?>
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card bg-dark text-white shadow">
                    <div class="card-body p-5 text-center">
                        <h2 class="text-info mb-3">Lecturer Control Panel</h2>
                        <p class="lead">Welcome back, Professor. Access your management tools below.</p>
                        <div class="mt-4 d-flex flex-wrap justify-content-center gap-3">
<a href="lecturer-units.php"
       class="btn btn-warning btn-lg">
        📚 My Units
    </a>

    <a href="create-assignment.php"
       class="btn btn-success btn-lg fw-bold">
        + Create Assignment
    </a>

    <a href="lecturer-assignments.php"
       class="btn btn-secondary btn-lg">
        📝 My Assignments
    </a>

    <a href="lecturer-submissions.php"
       class="btn btn-primary btn-lg">
        📥 View Submissions
    </a>

    <a href="manage-students.php"
       class="btn btn-info btn-lg">
        👨‍🎓 Manage Students
    </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php 
    // ======================== СЕКЦИЯ АДМИНА ========================
    elseif ($currentRole === "Admin"): 
    ?>
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card border-warning shadow-sm text-center py-5">
                    <i class="fas fa-user-shield fa-4x text-warning mb-3"></i>
                    <h3>Administrator Portal</h3>
                    <a href="admin-dashboard.php" class="btn btn-warning btn-lg px-5 mt-3">Go to Control Panel</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php include "includes/footer.php"; ?>