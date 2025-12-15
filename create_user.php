<?php
// PHP logic (to be added in a future lecture) would go here 
// to process the form submission and redirect the user.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New User</title>
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
                    
                    <div class="card-header bg-success text-white text-center rounded-top-3">
                        <h1 class="h4 mb-0">Create New User Profile</h1>
                    </div>
                    
                    <div class="card-body p-4">
                        
                        <!-- Form structure ready for future PHP processing -->
                        <!-- The action attribute will be set later to point to the processing file -->
                        <form action="includes/register-inc.php" method="POST">
                            
                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            
                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <small class="text-muted">Will be securely hashed upon creation.</small>
                            </div>

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <!-- Surname -->
                            <div class="mb-3">
                                <label for="surname" class="form-label">Surname</label>
                                <input type="text" class="form-control" id="surname" name="surname" required>
                            </div>
                            
                            <!-- Date of Birth -->
                            <div class="mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                            </div>

                               <!--Nationality-->
                            <div class="mb-3">
                                <label for="nationality" class="form-label">Nationality</label>
                                <input type="date" class="form-control" id="nationality" name="nationality">
                            </div>
                            
                            <!--email-->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>

                            <!-- Can Rate (Using a simple Checkbox for boolean status) -->
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" value="1" id="can_rate" name="can_rate">
                                <label class="form-check-label" for="can_rate">
                                    User is allowed to rate content
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg">Create User</button>
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