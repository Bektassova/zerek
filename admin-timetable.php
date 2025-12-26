<?php 
    include "includes/header.php";
    require_once 'includes/dbh.php';

    // Security Check: Only Admins allowed
    if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Admin") {
        header("location: login.php");
        exit();
    }

    // Fetch all students for the dropdown menu
    $sql = "SELECT user_id, name, surname FROM users WHERE role = 'Student'";
    $result = mysqli_query($conn, $sql);
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-calendar-plus"></i> Timetable Builder (Admin)</h4>
                </div>
                <div class="card-body">
                    
                    <?php
                    if (isset($_GET["status"]) && $_GET["status"] == "success") {
                        echo '<div class="alert alert-success">Class added successfully to the student\'s timetable!</div>';
                    }
                    ?>

                    <form action="includes/admin-timetable-inc.php" method="post">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Student:</label>
                            <select name="student_id" class="form-select" required>
                                <option value="">-- Select a Student --</option>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <option value="<?php echo $row['user_id']; ?>">
                                        <?php echo htmlspecialchars($row['name'] . " " . $row['surname'] . " (ID: " . $row['user_id'] . ")"); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Subject Name:</label>
                            <input type="text" name="subject" class="form-control" placeholder="e.g. Mathematics" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Day of the Week:</label>
                                <select name="day" class="form-select" required>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Start Time:</label>
                                <input type="time" name="time" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Room / Location:</label>
                            <input type="text" name="room" class="form-control" placeholder="e.g. Room 101-A" required>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="admin-dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                            <button type="submit" name="submit" class="btn btn-warning shadow-sm">Add to Timetable</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>