<?php
include "includes/header.php";
require_once "includes/dbh.php";

// Security: only logged-in students
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Student') {
    header("Location: login.php");
    exit();
}

$studentId = $_SESSION['userId'];

// assignment_id must come from URL
if (!isset($_GET['assignment_id'])) {
    echo "<p class='text-danger'>Assignment not selected.</p>";
    include "includes/footer.php";
    exit();
}

$assignmentId = (int) $_GET['assignment_id'];
?>

<div class="container mt-5">
    <h2>Submit Assignment</h2>

    <form method="post"
          action="includes/submit-assignment-inc.php"
          enctype="multipart/form-data">

        <input type="hidden" name="assignment_id" value="<?php echo $assignmentId; ?>">

        <div class="mb-3">
            <label class="form-label fw-bold">Upload your work</label>
            <input type="file"
                   name="files[]"
                   class="form-control"
                   multiple
                   required>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" name="submit_assignment" class="btn btn-success">
                Submit Assignment
            </button>

            <a href="profile.php" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php include "includes/footer.php"; ?>
