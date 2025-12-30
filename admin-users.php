<?php 
include "includes/header.php";
require_once 'includes/dbh.php';

/* =========================
   FILTER BY COURSE
========================= */
$selectedCourseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

/* =========================
   FETCH STUDENTS
========================= */
if ($selectedCourseId > 0) {
    $studentsSql = "
        SELECT * 
        FROM users 
        WHERE role = 'Student' AND course_id = ?
        ORDER BY name ASC
    ";
    $stmt = mysqli_prepare($conn, $studentsSql);
    mysqli_stmt_bind_param($stmt, "i", $selectedCourseId);
    mysqli_stmt_execute($stmt);
    $studentsResult = mysqli_stmt_get_result($stmt);
} else {
    $studentsSql = "
        SELECT * 
        FROM users 
        WHERE role = 'Student'
        ORDER BY name ASC
    ";
    $studentsResult = mysqli_query($conn, $studentsSql);
}

/* =========================
   FETCH COURSES
========================= */
$coursesSql = "SELECT * FROM courses ORDER BY course_name ASC";
$coursesResult = mysqli_query($conn, $coursesSql);
$allCourses = mysqli_fetch_all($coursesResult, MYSQLI_ASSOC);
?>

<div class="container mt-5 mb-5">
    <h2 class="mb-4">Student Enrollment Management</h2>

    <!-- FLASH MESSAGES -->
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $_SESSION['flash_success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo $_SESSION['flash_error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <!-- FILTER -->
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
                <button type="submit" class="btn btn-primary w-100">Apply</button>
            </div>

            <div class="col-md-2">
                <a href="admin-users.php" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </div>
    </form>

    <!-- =========================
         MASS ASSIGN FORM
    ========================= -->
    <form action="includes/enroll-students-bulk-inc.php" method="post">

        <div class="card shadow">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Current Course</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($student = mysqli_fetch_assoc($studentsResult)): ?> 
                        <tr>
                            <!--  Each checkbox stores the student's current course
     so we can detect reassignment on submit -->
     <td>
    <input type="checkbox"
           name="student_ids[]"
           value="<?php echo $student['user_id']; ?>"
           data-current-course="<?php echo htmlspecialchars($currentCourse); ?>">
</td>

                            <td>
                                <input type="checkbox"
       name="student_ids[]"
       value="<?php echo $student['user_id']; ?>"
       data-current-course="<?php echo htmlspecialchars($currentCourse); ?>">

                            </td>

                            <td>
                                <?php echo htmlspecialchars($student['name'] . ' ' . $student['surname']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($student['email']); ?>
                            </td>

                            <td>
                                <span class="badge bg-secondary">
                                    <?php
                                        $currentCourse = "Not Assigned";
                                        foreach ($allCourses as $c) {
                                            if ($c['course_id'] == $student['course_id']) {
                                                $currentCourse = $c['course_name'];
                                                break;
                                            }
                                        }
                                        echo htmlspecialchars($currentCourse);
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- MASS ASSIGN CONTROLS -->
                <div class="d-flex gap-2 mt-3">
                    <select name="course_id" class="form-select w-auto" required>
                        <option value="">-- Select course --</option>
                        <?php foreach ($allCourses as $c): ?>
                            <option value="<?php echo $c['course_id']; ?>">
                                <?php echo htmlspecialchars($c['course_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

<!-- To add an ID to the bulk assign button
     so JavaScript can intercept the submit action -->
     <button type="submit"
name="bulk_assign"
class="btn btn-success"
id="bulkAssignBtn">
Assign selected students
</button>
</div>

</div>
</div>

</form>
</div>


<!--JS работает только при клике на -> selectAll-->
<script>
document.getElementById('selectAll').addEventListener('change', function () {
    document.querySelectorAll('input[name="student_ids[]"]').forEach(cb => {
        cb.checked = this.checked;
    });
});
</script>

<!-- Before submitting the form, JavaScript checks:
     - if selected students already have a course
     - and if a different course is selected
     If so, a confirmation dialog is shown to prevent accidental reassignment -->

<script>
document.querySelector('form[action="includes/enroll-students-bulk-inc.php"]')
    .addEventListener('submit', function (e) {

        const selectedCourse = this.querySelector('select[name="course_id"]');
        const selectedCourseText = selectedCourse.options[selectedCourse.selectedIndex].text;

        let reassignedStudents = [];

        document.querySelectorAll('input[name="student_ids[]"]:checked').forEach(cb => {
            const currentCourse = cb.dataset.currentCourse;

            if (currentCourse && currentCourse !== "Not Assigned" && currentCourse !== selectedCourseText) {
                reassignedStudents.push(
                    `• ${currentCourse} → ${selectedCourseText}`
                );
            }
        });

        if (reassignedStudents.length > 0) {
            const message =
                "Some students are already assigned to another course:\n\n" +
                reassignedStudents.join("\n") +
                "\n\nDo you want to reassign them?";

            if (!confirm(message)) {
                e.preventDefault();
            }
        }
    });
</script>



<?php include "includes/footer.php"; ?>
