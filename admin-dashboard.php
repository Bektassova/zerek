<?php 
    include "includes/header.php";
    require_once 'includes/dbh.php';

    // SECURITY CHECK: Must be logged in AND must be an Admin
    if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Admin") {
        header("location: login.php?error=notauthorized");
        exit();
    }
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h2 class="border-bottom pb-3">Admin Control Panel</h2>
            <p class="text-muted">Welcome, Administrator. What would you like to manage today?</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-graduate fa-3x text-primary mb-3"></i>
                    <h5>Manage Students</h5>
                    <p class="small text-muted">Register new students or edit existing student profiles.</p>
                    <a href="admin-users.php" class="btn btn-primary btn-sm">Enter</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-book fa-3x text-success mb-3"></i>
                    <h5>Courses & Units</h5>
                    <p class="small text-muted">Create the subjects and units for the semester.</p>
                   <a href="admin-courses.php" class="btn btn-primary">Manage Courses</a>
<a href="admin-units.php" class="btn btn-secondary">Manage Units</a>

                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-plus fa-3x text-warning mb-3"></i>
                    <h5>Timetable Builder</h5>
                    <p class="small text-muted">Assign courses to students and set class times.</p>
                  <a href="admin-timetable.php" class="btn btn-warning btn-sm">Enter</a>

                   

                </div>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>