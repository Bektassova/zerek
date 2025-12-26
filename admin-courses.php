<?php 
include "includes/header.php";
require_once 'includes/dbh.php';

// Security check
if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Admin") {
    header("location: login.php");
    exit();
}

/* =========================
   QUERY 1: Active Academic Structure
   ========================= */
$unitsSql = "
SELECT 
    units.unit_id,
    units.unit_name, 
    units.unit_description, 
    courses.course_name 
FROM units 
LEFT JOIN courses ON units.course_id = courses.course_id
ORDER BY units.unit_name ASC
";

$unitsResult = mysqli_query($conn, $unitsSql);

/* =========================
   QUERY 2: Courses + unit count
   ========================= */
$coursesSql = "
    SELECT 
        courses.course_id,
        courses.course_name,
        COUNT(units.unit_id) AS unit_count
    FROM courses
    LEFT JOIN units ON courses.course_id = units.course_id
    GROUP BY courses.course_id
    ORDER BY courses.course_name ASC
";
$coursesResult = mysqli_query($conn, $coursesSql);
?>

<div class="container mt-5 mb-5">
    <div class="row">

        <!-- LEFT COLUMN -->
        <div class="col-md-4">

            <!-- STEP 1 -->
            <div class="card shadow-sm mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Step 1: Create Course</h5>
                </div>
                <div class="card-body">
                    <form action="includes/admin-course-add-inc.php" method="post">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Course Name</label>
                            <input type="text" name="course_name" class="form-control" placeholder="e.g. Business Studies" required>
                        </div>
                        <button type="submit" name="submit_course" class="btn btn-primary w-100">
                            Add Course to System
                        </button>
                    </form>
                </div>
            </div>

            <!-- STEP 2 -->
            <div class="card shadow-sm mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Step 2: Add Unit to Course</h5>
                </div>
                <div class="card-body">
                    <form action="includes/admin-units-inc.php" method="post">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Unit Name</label>
                            <input type="text" name="unit_name" class="form-control" placeholder="e.g. Management" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Assign to Course</label>
                            <select name="course_id" class="form-select" required>
                                <option value="">-- Select Course --</option>
                                <?php 
                                mysqli_data_seek($coursesResult, 0);
                                while ($course = mysqli_fetch_assoc($coursesResult)): ?>
                                    <option value="<?php echo $course['course_id']; ?>">
                                        <?php echo htmlspecialchars($course['course_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="unit_description" class="form-control" rows="2"></textarea>
                        </div>

                        <button type="submit" name="submit" class="btn btn-success w-100">
                            Link Unit to Course
                        </button>
                    </form>
                </div>
            </div>

            <!-- MANAGE COURSES -->
            <div class="card shadow-sm mb-4 border-danger">
                <div class="card-header bg-danger text-white py-2">
                    <h6 class="mb-0">Manage Courses (Delete / Clean-up)</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Course</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                           mysqli_data_seek($coursesResult, 0); // Снова сбрасываем, т.к. использовалось выше
                            // Используем переменную $coursesResult (запрос №2)
                            while($c = mysqli_fetch_assoc($coursesResult)): 
                                $hasUnits = ($c['unit_count'] > 0);
                            ?>
                                <tr>
                                    <td><?php echo $c['course_id']; ?></td>
                                    <td><?php echo htmlspecialchars($c['course_name']); ?></td>
                                    <td>
                                        <?php if ($hasUnits): ?>
                                            <button class="btn btn-secondary btn-sm" disabled title="Cannot delete: Course has units">
                                                <i class="fas fa-lock"></i> Locked (<?php echo $c['unit_count']; ?> Units)
                                            </button>
                                        <?php else: ?>
                                            <a href="includes/delete-course-inc.php?id=<?php echo $c['course_id']; ?>" 
                                               class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Are you sure you want to delete this empty course?')">
                                               Delete
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Active Academic Structure</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                 <thead>
    <tr>
        <th>Unit</th>
        <th>Course</th>
        <th>Description</th>
        <th>Action</th> </tr>
</thead>
<tbody>
    <?php while ($row = mysqli_fetch_assoc($unitsResult)): ?>
        <tr>
            <td><strong><?php echo htmlspecialchars($row['unit_name']); ?></strong></td>
            <td>
                <span class="badge bg-info text-dark">
                    <?php echo htmlspecialchars($row['course_name'] ?? 'Unassigned'); ?>
                </span>
            </td>
            <td>
                <small><?php echo htmlspecialchars($row['unit_description']); ?></small>
            </td>
         <td class="text-nowrap">
    <a href="edit-unit.php?id=<?php echo $row['unit_id']; ?>"
       class="btn btn-sm btn-outline-primary me-2">
        Edit
    </a>

    <a href="includes/delete-unit-inc.php?id=<?php echo $row['unit_id']; ?>"
       class="btn btn-sm btn-outline-danger"
       onclick="return confirm('Delete this unit?')">
        <i class="fas fa-trash-alt"></i> Delete
    </a>
</td>


        </tr>
    <?php endwhile; ?>
</tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include "includes/footer.php"; ?>
