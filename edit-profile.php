<?php 
    // 1. Load the header (this starts the session)
    include "includes/header.php";
    
    // 2. Load the database connection and functions
    require_once 'includes/dbh.php';
    require_once 'includes/functions.php';

    // 3. Security Gatekeeper
    if (!isset($_SESSION["userId"])) {
        header("location: login.php");
        exit();
    }

    // 4. Fetch the user's current data
    $user = getUser($conn, $_SESSION["userId"]);

    // If the user data couldn't be fetched, display an error and stop
    if (!$user) {
        echo "<div class='container mt-5'><div class='alert alert-danger'>Error: Could not retrieve user data from the database.</div></div>";
        include "includes/footer.php";
        exit();
    }
?>


<form action="includes/edit-profile-inc.php" method="post">
    <div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Edit Personal Information</h4>
                </div>
                <div class="card-body p-4">
                    <form action="includes/edit-profile-inc.php" method="post">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Username (Cannot be changed)</label>
                            <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">First Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Surname</label>
                                <input type="text" name="surname" class="form-control" value="<?php echo htmlspecialchars($user['surname']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>" required>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="profile.php" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" name="submit" class="btn btn-success px-4">Save All Changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-danger mt-4 mb-5">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                         <h4 class="text-danger">Danger Zone</h4>
                        <h5 class="text-danger mb-0">Delete Account</h5>
                        <small class="text-muted">Once deleted, your data cannot be recovered.</small>
                    </div>
                    <a href="includes/delete-account-inc.php" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to permanently delete your account?')">Delete Account</a>
                </div>
            </div>
        </div>
    </div>
</div>
</form>

<hr>
