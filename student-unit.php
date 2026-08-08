<?php
session_start();

require_once "includes/dbh.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

error_reporting(E_ALL);
ini_set("display_errors", 1);

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

$studentId = (int)$_SESSION["userId"];

// =====================================================
// GET UNIT ID
// =====================================================

if (!isset($_GET["unit_id"])) {
    die("Unit ID is missing.");
}

$unitId = (int)$_GET["unit_id"];

// =====================================================
// CHECK STUDENT ENROLMENT + GET UNIT
// =====================================================

$sql = "
SELECT
    u.unit_id,
    u.unit_name,
    u.unit_description
FROM student_units su
INNER JOIN units u
    ON u.unit_id = su.unit_id
WHERE su.student_id = ?
AND su.unit_id = ?
LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $studentId,
    $unitId
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$unit = mysqli_fetch_assoc($result);

if (!$unit) {
    die("You are not enrolled in this unit.");
}

// =====================================================
// GET ONLY PUBLISHED WEEKLY PLAN ITEMS
// =====================================================

$sqlWeeks = "
SELECT
    ti.id,
    ti.topic_title,
    ti.planned_date,
    ti.order_index,
    ti.learning_objectives,
    ti.lecture_notes,
    ti.powerpoint,
    ti.tutorial,
    ti.reading_material,
    ti.quiz_link,
    ti.homework,
    ti.description,
    ti.is_published

FROM thematic_items ti

INNER JOIN thematic_plans tp
    ON tp.id = ti.plan_id

WHERE tp.unit_id = ?
AND ti.is_published = 1

ORDER BY
    ti.order_index ASC,
    ti.id ASC
";

$stmtWeeks = mysqli_prepare($conn, $sqlWeeks);

mysqli_stmt_bind_param(
    $stmtWeeks,
    "i",
    $unitId
);

mysqli_stmt_execute($stmtWeeks);

$resultWeeks = mysqli_stmt_get_result($stmtWeeks);
// =====================================================
// HEADER
// =====================================================

require_once "includes/header.php";
?>

<div class="container mt-5">

    <!-- =================================================
         UNIT HEADER
         ================================================= -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold">
                <?php
                echo htmlspecialchars($unit["unit_name"]);
                ?>
            </h2>

            <p class="text-muted mb-0">

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

        </div>

        <a
            href="student-units.php"
            class="btn btn-secondary"
        >
            ← Back to My Units
        </a>

    </div>


    <!-- =================================================
         WEEKLY TEACHING PLAN
         ================================================= -->

    <div class="card shadow">

        <div class="card-header bg-dark text-white">

            <h5 class="mb-0">
                Weekly Teaching Plan
            </h5>

        </div>

        <div class="card-body">

            <?php if (mysqli_num_rows($resultWeeks) > 0): ?>

                <?php while ($week = mysqli_fetch_assoc($resultWeeks)): ?>

                    <div class="card mb-4 border">

                        <div class="card-body">

                            <!-- WEEK + TOPIC -->

                            <h5 class="fw-bold mb-3">

                                Week
                                <?php
                                echo (int)$week["order_index"];
                                ?>

                                —
                                <?php
                                echo htmlspecialchars(
                                    $week["topic_title"]
                                );
                                ?>

                            </h5>


                            <!-- DESCRIPTION -->

                            <?php if (!empty($week["description"])): ?>

                                <div class="mb-3">

                                    <strong>
                                        Description
                                    </strong>

                                    <p class="text-muted mb-0">

                                        <?php
                                        echo nl2br(
                                            htmlspecialchars(
                                                $week["description"]
                                            )
                                        );
                                        ?>

                                    </p>

                                </div>

                            <?php endif; ?>


                            <!-- LEARNING OBJECTIVES -->

                            <?php if (!empty($week["learning_objectives"])): ?>

                                <div class="mb-3">

                                    <strong>
                                        Learning Objectives
                                    </strong>

                                    <p class="text-muted mb-0">

                                        <?php
                                        echo nl2br(
                                            htmlspecialchars(
                                                $week["learning_objectives"]
                                            )
                                        );
                                        ?>

                                    </p>

                                </div>

                            <?php endif; ?>


                            <!-- HOMEWORK -->

                            <?php if (!empty($week["homework"])): ?>

                                <div class="mb-3">

                                    <strong>
                                        Homework
                                    </strong>

                                    <p class="text-muted mb-0">

                                        <?php
                                        echo nl2br(
                                            htmlspecialchars(
                                                $week["homework"]
                                            )
                                        );
                                        ?>

                                    </p>

                                </div>

                            <?php endif; ?>
                                                        <!-- =================================================
                                 MATERIALS
                                 ================================================= -->

                            <?php
                            $hasMaterials =
                                !empty($week["lecture_notes"]) ||
                                !empty($week["powerpoint"]) ||
                                !empty($week["tutorial"]) ||
                                !empty($week["reading_material"]) ||
                                !empty($week["quiz_link"]);
                            ?>

                            <?php if ($hasMaterials): ?>

                                <div class="mt-4">

                                    <strong class="d-block mb-3">
                                        Materials
                                    </strong>


                                    <!-- Lecture Notes -->

                                    <?php if (!empty($week["lecture_notes"])): ?>

                                        <a
                                            href="<?php echo htmlspecialchars($week["lecture_notes"]); ?>"
                                            target="_blank"
                                            class="btn btn-outline-primary me-2 mb-2"
                                        >
                                            📄 Lecture Notes
                                        </a>

                                    <?php endif; ?>


                                    <!-- PowerPoint -->

                                    <?php if (!empty($week["powerpoint"])): ?>

                                        <a
                                            href="<?php echo htmlspecialchars($week["powerpoint"]); ?>"
                                            target="_blank"
                                            class="btn btn-outline-danger me-2 mb-2"
                                        >
                                            📊 PowerPoint
                                        </a>

                                    <?php endif; ?>


                                    <!-- Tutorial -->

                                    <?php if (!empty($week["tutorial"])): ?>

                                        <a
                                            href="<?php echo htmlspecialchars($week["tutorial"]); ?>"
                                            target="_blank"
                                            class="btn btn-outline-success me-2 mb-2"
                                        >
                                            📝 Tutorial
                                        </a>

                                    <?php endif; ?>


                                    <!-- Reading Material -->

                                    <?php if (!empty($week["reading_material"])): ?>

                                        <a
                                            href="<?php echo htmlspecialchars($week["reading_material"]); ?>"
                                            target="_blank"
                                            class="btn btn-outline-secondary me-2 mb-2"
                                        >
                                            📚 Reading Material
                                        </a>

                                    <?php endif; ?>


                                    <!-- Quiz -->

                                    <?php if (!empty($week["quiz_link"])): ?>

                                        <a
                                            href="<?php echo htmlspecialchars($week["quiz_link"]); ?>"
                                            target="_blank"
                                            class="btn btn-outline-warning me-2 mb-2"
                                        >
                                            📝 Quiz
                                        </a>

                                    <?php endif; ?>

                                </div>

                            <?php else: ?>

                                <span class="badge bg-secondary">
                                    No materials available
                                </span>

                            <?php endif; ?>

                        </div>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="alert alert-info mb-0">
                    No published weekly plan is available yet.
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php
require_once "includes/footer.php";
?>