<?php
include "includes/header.php";
require_once "includes/dbh.php";

// Admin only
if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Admin") {
    header("location: login.php");
    exit();
}
?>

<div class="container mt-5">
    <h2 class="mb-4">📅 Timetable Builder (Admin)</h2>

    <!-- ADD CLASS FORM -->
    <form method="post" action="includes/admin-timetable-inc.php" class="row g-3 mb-4">

        <div class="col-md-2">
            <label class="form-label">Day</label>
            <select name="class_day" class="form-select" required>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Start Time</label>
            <input type="time" name="start_time" class="form-control" required>
        </div>

        <div class="col-md-2">
            <label class="form-label">End Time</label>
            <input type="time" name="end_time" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Subject</label>
            <input type="text" name="subject_name" class="form-control" required>
        </div>

        <div class="col-md-2">
            <label class="form-label">Room</label>
            <input type="text" name="room_number" class="form-control">
        </div>

        <div class="col-md-1">
            <label class="form-label">Type</label>
            <select name="class_type" class="form-select">
                <option>Lecture</option>
                <option>Seminar</option>
                <option>Workshop</option>
                <option>Practical</option>
            </select>
        </div>

        <div class="col-12">
            <button type="submit" name="add_class" class="btn btn-success">
                ➕ Add to Timetable
            </button>
            <a href="admin-dashboard.php" class="btn btn-secondary ms-2">
                Back
            </a>
        </div>
    </form>

    <hr>

    <p class="text-muted">
        Use the form above to recreate schedules like:
        <br>Mon 09:00–10:30 Programming I (Lab 102)
    </p>
</div>

<?php include "includes/footer.php"; ?>
