<?php
// In a future lecture, this is where you would:
// 1. Get the user ID from the URL (e.g., ?id=5)
// 2. Query the database to fetch the existing user data ($user)
// 3. Populate the form fields using $user['field_name']
$user_id = 1; // Placeholder ID for display purposes
$placeholder_data = [
    'username' => 'existingUser123',
    'name' => 'John',
    'surname' => 'Doe',
    'date_of_birth' => '1990-05-15',
    'can_rate' => 1, // 1 for checked, 0 for unchecked
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Profile</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body { background-color: #f8f9fa; }
        .form-card { max-width: 600px; }
    </style>
</head>
<body>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card form-card shadow-lg border-0 rounded-3 mx-auto">
                    
                    <div class="card-header bg-primary text-white text-center rounded-top-3">
                        <h1 class="h4 mb-0">Edit User Profile (ID: <?php echo $user_id; ?>)</h1>
                    </div>
                    
                    <div class="card-body p-4">
                        
                        <!-- Form structure ready for future PHP processing -->
                        <form action="process_update.php" method="POST">
                            
                            <!-- User ID (Required for updates, often hidden) -->
                            <input type="hidden" name="id" value="<?php echo $user_id; ?>">
                            
                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <!-- PHP will eventually dynamically populate the value -->
                                <input type="text" class="form-control" id="username" name="username" required value="<?php echo $placeholder_data['username']; ?>">
                            </div>
                            
                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password (Leave Blank to Keep Old)</label>
                                <!-- Note: Passwords are usually left blank during edits for security -->
                                <input type="password" class="form-control" id="password" name="password">
                                <small class="text-muted">Enter a new password only if you wish to change it.</small>
                            </div>

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="name" name="name" required value="<?php echo $placeholder_data['name']; ?>">
                            </div>

                            <!-- Surname -->
                            <div class="mb-3">
                                <label for="surname" class="form-label">Surname</label>
                                <input type="text" class="form-control" id="surname" name="surname" required value="<?php echo $placeholder_data['surname']; ?>">
                            </div>
                            
                            <!-- Date of Birth -->
                            <div class="mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo $placeholder_data['date_of_birth']; ?>">
                            </div>

                            <!-- Can Rate (Checked attribute populated by PHP) -->
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" value="1" id="can_rate" name="can_rate" 
                                    <?php echo ($placeholder_data['can_rate'] == 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="can_rate">
                                    User is allowed to rate content
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>