<?php
session_start();

require_once "includes/dbh.php";

// =====================================================
// SECURITY
// =====================================================

if (
    !isset($_SESSION["userId"]) ||
    $_SESSION["role"] !== "Student"
) {
    header("Location: login.php");
    exit();
}

$studentId = $_SESSION["userId"];

// =====================================================
// GET STUDENT'S UNITS
// =====================================================

$sql = "
SELECT
    u.unit_id,
    u.unit_name,
    u.unit_description
FROM student_units su
JOIN units u
    ON u.unit_id = su.unit_id
WHERE su.student_id = ?
ORDER BY u.unit_name ASC
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $studentId
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

// =====================================================
// HEADER
// =====================================================

require_once "includes/header.php";
?>

<div class="container mt-5">

    <!-- PAGE HEADER -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold">
                My Units
            </h2>

            <p class="text-muted mb-0">
                Units you are currently enrolled in.
            </p>

        </div>

        <a
            href="profile.php"
            class="btn btn-secondary"
        >
            ← Back to Dashboard
        </a>

    </div>


    <!-- UNITS -->

    <div class="row g-4">

        <?php if (mysqli_num_rows($result) > 0): ?>

            <?php while ($unit = mysqli_fetch_assoc($result)): ?>

                <div class="col-md-6 col-lg-4">

                    <div class="card shadow-sm h-100">

                        <div class="card-body d-flex flex-column">

                            <h5 class="card-title fw-bold">
                                <?php
                                echo htmlspecialchars(
                                    $unit["unit_name"]
                                );
                                ?>
                            </h5>

                            <p class="card-text text-muted small">

                                <?php if (!empty($unit["unit_description"])): ?>

                                    <?php
                                    echo htmlspecialchars(
                                        $unit["unit_description"]
                                    );
                                    ?>

                                <?php else: ?>

                                    No description available.

                                <?php endif; ?>

                            </p>

                            <div class="mt-auto pt-3">

                                <a
                                    href="student-unit.php?unit_id=<?php echo (int)$unit["unit_id"]; ?>"
                                    class="btn btn-primary w-100"
                                >
                                    View Unit
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="col-12">

                <div class="alert alert-info">
                    You are not currently enrolled in any units.
                </div>

            </div>

        <?php endif; ?>

    </div>

</div>


<?php
require_once "includes/footer.php";
?>