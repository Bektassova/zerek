<?php 
include "includes/header.php";
require_once 'includes/dbh.php';

// Get selected course from filter (if any)
$selectedCourseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;


// 1. Fetch all students
if ($selectedCourseId > 0) {
    $studentsSql = "
        SELECT * 
        FROM users 
        WHERE role = 'Student' AND course_id = $selectedCourseId
        ORDER BY name ASC
    ";
} else {
    $studentsSql = "
        SELECT * 
        FROM users 
        WHERE role = 'Student'
        ORDER BY name ASC
    ";
}

$studentsResult = mysqli_query($conn, $studentsSql);


// 2. Fetch all courses for the dropdown
$coursesSql = "SELECT * FROM courses ORDER BY course_name ASC";
$coursesResult = mysqli_query($conn, $coursesSql);
$allCourses = mysqli_fetch_all($coursesResult, MYSQLI_ASSOC);
?>

<div class="container mt-5">
    <h2 class="mb-4">Student Enrollment Management</h2>

    <?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['flash_success']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['flash_error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>


    <!-- 🔹 FILTER BY COURSE -->
    <form method="get" class="mb-4">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold">Filter by course</label>
                <select name="course_id" class="form-select">
                    <option value="0">All courses</option>
                    <?php foreach ($allCourses as $course): ?>
                        <option value="<?php echo $course['course_id']; ?>"
                            <?php if ($selectedCourseId == $course['course_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($course['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
    <button type="submit" class="btn btn-primary w-100">
        Apply
    </button>
</div>

<div class="col-md-2">
    <a href="admin-users.php" class="btn btn-outline-secondary w-100">
        Reset
    </a>
</div>

        </div>
    </form>

    <!-- ⬇️ ТАБЛИЦА СТУДЕНТОВ -->
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-hover">
                
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Current Course</th>
                        <th>Assign New Course</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($student = mysqli_fetch_assoc($studentsResult)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['name'] . ' ' . $student['surname']); ?></td>
                       <td><?php echo htmlspecialchars($student['email']); ?></td>

                        <td>
                            <span class="badge bg-secondary">
                                <?php 
                                    // Logic to find current course name
                                    $currentCourse = "Not Assigned";
                                    foreach($allCourses as $c) {
                                        if($c['course_id'] == $student['course_id']) $currentCourse = $c['course_name'];
                                    }
                                    echo htmlspecialchars($currentCourse);
                                ?>
                            </span>
                        </td>
                        <td>
                            <form action="includes/enroll-student-inc.php" method="post" class="d-flex gap-2">
                                <input type="hidden" name="user_id" value="<?php echo $student['user_id']; ?>">
                                <select name="course_id" class="form-select form-select-sm">
                                    <option value="">-- Select Course --</option>
                                    <?php foreach($allCourses as $c): ?>
                                        <option value="<?php echo $c['course_id']; ?>">
                                            <?php echo htmlspecialchars($c['course_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="submit_enroll" class="btn btn-primary btn-sm">Assign</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>