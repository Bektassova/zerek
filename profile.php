 <?php 
    // 1. Load the header (this starts the session)
    include "includes/header.php";
    
    // 2. Load the database connection and functions
    require_once 'includes/dbh.php';
    require_once 'includes/functions.php';

    // 3. THE GATEKEEPER: Check security
    if (!isset($_SESSION["userId"])) {
        header("location: login.php?error=notloggedin");
        exit();
    }

    // 4. Fetch the specific user's data using the ID stored in the session
    $userId = $_SESSION["userId"];
    $user = getUser($conn, $userId); 

    // 5. Emergency check if user is missing from DB
    if (!$user) {
        echo "<p>Error: Could not load user profile.</p>";
        include "includes/footer.php";
        exit();
    }
?>

<div class="container mt-5">
    <div class="row align-items-center">
        <div class="col-md-7">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-circle"></i> Personal Information</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Username:</div>
                        <div class="col-sm-8 text-muted"><?php echo htmlspecialchars($user['username']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Full Name:</div>
                        <div class="col-sm-8 text-muted"><?php echo htmlspecialchars($user['name'] . " " . $user['surname']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Email:</div>
                        <div class="col-sm-8 text-muted"><?php echo htmlspecialchars($user['email']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Date of Birth:</div>
                        <div class="col-sm-8"><span class="badge bg-info text-dark"><?php echo htmlspecialchars($user['date_of_birth']); ?></span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">User ID:</div>
                        <div class="col-sm-8"><span class="badge bg-info text-dark"><?php echo htmlspecialchars($user['user_id']); ?></span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Role:</div>
                        <div class="col-sm-8"><span class="badge bg-info text-dark"><?php echo htmlspecialchars($user['role']); ?></span></div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 mb-4">
                <a href="edit-profile.php" class="btn btn-primary px-4 shadow-sm">
                    <i class="fas fa-edit"></i> Edit My Information
                </a>
            </div>
        </div>

        <div class="col-md-5 d-none d-md-block text-center">
            <img src="https://cache.careers360.mobi/media/article_images/2023/2/3/importance-of-education.jpg" 
                 alt="Education" 
                 class="img-fluid rounded-3 shadow" 
                 style="max-height: 300px; width: 100%; object-fit: cover;">
        </div>
    </div> 

    <div class="row mt-5">
        <div class="col-12">
            <hr class="mb-5">
            <h3 class="mb-4">Dashboard Overview</h3>

            <?php if ($user['role'] == "Student"): ?>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card border-start border-primary border-4 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-primary">My Timetable</h5>
                                <p class="card-text text-muted">Check your upcoming lectures and classroom locations.</p>
                                <a href="timetable.php" class="btn btn-sm btn-outline-primary">View Schedule</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card border-start border-success border-4 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-success">Latest Grades</h5>
                                <p class="card-text text-muted">Your semester results have been updated.</p>
                                <a href="grades.php" class="btn btn-sm btn-outline-success">View Academic Report</a>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($user['role'] == "Lecturer"): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card bg-dark text-white shadow">
                            <div class="card-body p-4">
                                <h4 class="card-title">Lecturer Portal</h4>
                                <p class="card-text">
    Welcome back, Professor. You can manage your students or upload new course materials below.
</p>

<div class="mt-3 d-flex flex-wrap">
    <a href="manage-students.php" class="btn btn-info me-2 mb-2 flex-grow-1">
        Manage Students
    </a>
    <a href="upload-content.php" class="btn btn-light me-2 mb-2 flex-grow-1">
        Upload Resources
    </a>
    <a href="create-assignment.php" class="btn btn-success me-2 mb-2 flex-grow-1">
        Create Assignment
    </a>
    <a href="lecturer-assignments.php" class="btn btn-secondary mb-2 flex-grow-1">
        My Assignments
    </a>
</div>

                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($user['role'] == "Admin"): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card border-warning shadow-sm">
                <div class="card-body bg-light text-center py-5">
                    <i class="fas fa-user-shield fa-4x text-warning mb-3"></i>
                    <h3 class="card-title">Administrator Portal</h3>
                    <p class="card-text">You have full access to manage students, courses, and the timetable.</p>
                    <a href="admin-dashboard.php" class="btn btn-warning btn-lg px-5">Go to Admin Control Panel</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>

    


