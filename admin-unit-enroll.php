<?php
session_start();
include "includes/header.php";
require_once "includes/dbh.php";

// 1️⃣ Проверка unit_id
if (!isset($_GET['unit_id'])) {
    echo "<p class='text-danger'>Unit not selected.</p>";
    include "includes/footer.php";
    exit();
}

$unitId = (int) $_GET['unit_id'];

// 2️⃣ Получаем unit
$unitSql = "SELECT * FROM units WHERE unit_id = ?";
$stmt = mysqli_prepare($conn, $unitSql);
mysqli_stmt_bind_param($stmt, "i", $unitId);
mysqli_stmt_execute($stmt);
$unitResult = mysqli_stmt_get_result($stmt);
$unit = mysqli_fetch_assoc($unitResult);

if (!$unit) {
    echo "<p class='text-danger'>Unit not found.</p>";
    include "includes/footer.php";
    exit();
}

// 3️⃣ Получаем студентов
$studentsSql = "
    SELECT user_id, name, surname, email
    FROM users
    WHERE role = 'Student'
    ORDER BY name ASC
";
$studentsResult = mysqli_query($conn, $studentsSql);
?>

<div class="container mt-5">
  <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['flash_success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <h2>Enroll students to unit</h2>

    <h4 class="text-muted">
        <?php echo htmlspecialchars($unit['unit_name']); ?>
    </h4>

    <form method="post" action="includes/enroll-students-to-unit-inc.php">
        <input type="hidden" name="unit_id" value="<?php echo $unitId; ?>">

        <table class="table table-hover mt-4">
            <thead class="table-dark">
                <tr>
                    <th></th>
                    <th>Student</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($student = mysqli_fetch_assoc($studentsResult)): ?>
                    <tr>
                        <td>
                            <input
                                type="checkbox"
                                name="student_ids[]"
                                value="<?php echo $student['user_id']; ?>"
                            >
                        </td>
                        <td>
                            <?php
                            echo htmlspecialchars(
                                $student['name'] . ' ' . $student['surname']
                            );
                            ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($student['email']); ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
<div class="mt-4 d-flex gap-2">
    <button type="submit" name="enroll_students" class="btn btn-success">
        Enroll selected students
    </button>

    <a href="admin-units.php" class="btn btn-secondary">
        Cancel
    </a>
</div>

    </form>
</div>

<?php include "includes/footer.php"; ?>
