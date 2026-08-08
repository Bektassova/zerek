<?php
session_start();
require_once "includes/dbh.php";

// Security
if (
    !isset($_SESSION["userId"]) ||
    ($_SESSION["role"] !== "Lecturer" && $_SESSION["role"] !== "Teacher")
) {
    header("location: login.php?error=notauthorized");
    exit();
}

$lecturerId = $_SESSION["userId"];

/*
|--------------------------------------------------------------------------
| Load units taught by this lecturer
|--------------------------------------------------------------------------
| Assumption:
| units.lecturer_id = users.user_id
|--------------------------------------------------------------------------
*/

$sql = "
SELECT
    u.unit_id,
    u.unit_name,
    u.unit_description
FROM units u
INNER JOIN lecturer_units lu
    ON u.unit_id = lu.unit_id
WHERE lu.lecturer_id = ?
ORDER BY u.unit_name ASC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $lecturerId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

include "includes/header.php";
?>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>
            <i class="fas fa-book text-primary"></i>
            My Units
        </h2>

        <a href="profile.php"
           class="btn btn-outline-secondary">
            Back to Profile
        </a>

    </div>


    <?php if(mysqli_num_rows($result)>0): ?>

        <div class="row">

        <?php while($unit=mysqli_fetch_assoc($result)): ?>

            <div class="col-lg-4 col-md-6 mb-4">

                <div class="card shadow-sm h-100">

                    <div class="card-header bg-primary text-white">

                        <h5 class="mb-0">

                            <?php echo htmlspecialchars($unit['unit_name']); ?>

                        </h5>

                    </div>

                    <div class="card-body">

                       <p class="text-muted mb-2">
    <strong>Description</strong>
</p>

<p>
    <?php
    echo !empty($unit['unit_description'])
        ? htmlspecialchars($unit['unit_description'])
        : "No description available.";
    ?>
</p>

                        <div class="d-grid gap-2">

                            <a href="lecturer-weekly-plan.php?unit_id=<?php echo $unit['unit_id']; ?>"
                               class="btn btn-success">

                                📅 Weekly Plan

                            </a>

                            <a href="create-assignment.php?unit_id=<?php echo $unit['unit_id']; ?>"
                               class="btn btn-outline-primary">

                                📝 Create Assignment

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="alert alert-warning">

            No units have been assigned to you yet.

        </div>

    <?php endif; ?>

</div>

<?php include "includes/footer.php"; ?>