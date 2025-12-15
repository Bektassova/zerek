<?php 
    include "includes/header.php";
    require_once 'includes/functions.php';

    // Start a session and check if the user is logged in
    session_start();
    if (!isset($_SESSION["userId"])) {
        header("location: login.php");
        exit();
    }

    // Connect to the database
    require_once 'includes/dbh.php';
    
    // Fetch the specific user's data using the ID stored in the session
    $userId = $_SESSION["userId"];
    $user = getUser($conn, $userId); 

    if (!$user) {
        echo "<p>Error: Could not load user profile.</p>";
        include "includes/footer.php";
        exit();
    }
?>

<div class="container mt-5">
    <div class="row align-items-center mb-4">
        
        <div class="col-2">
            <img 
                src="images/user.png" 
                alt="Default User Icon"
                style="height:100px;width:100px; border-radius: 50%; object-fit: cover;" 
            >
        </div>
        <div class="col-10">
            <h1>Welcome, <?php echo htmlspecialchars($user["name"]); ?>!</h1>
            <p class="lead">Your role is: **<?php echo htmlspecialchars($user["role"]); ?>**</p>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Personal Information
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Username:</strong> <?php echo htmlspecialchars($user["username"]); ?></li>
                    <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($user["email"]); ?></li>
                    <li class="list-group-item"><strong>Full Name:</strong> <?php echo htmlspecialchars($user["name"] . " " . $user["surname"]); ?></li>
                    <li class="list-group-item"><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user["date_of_birth"]); ?></li>
                    <li class="list-group-item"><strong>User ID:</strong> <?php echo htmlspecialchars($user["user_id"]); ?></li>
                </ul>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Dashboard Overview
                </div>
                <div class="card-body">
                    <p>This is where widgets like your Timetable, latest Grades, or notifications (as seen in your wireframes) will go.</p>
                    <a href="#" class="btn btn-sm btn-info">View Full Dashboard</a>
                </div>
            </div>
        </div>
    </div>
    
</div>

<?php include "includes/footer.php"; ?>

    


<?php include "includes/footer.php";?>