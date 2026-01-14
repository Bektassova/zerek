<?php
session_start();
require_once "includes/dbh.php";

/*
====================================================
SECURITY CHECK
----------------------------------------------------
Only logged-in users with role = Lecturer
are allowed to access this page.
====================================================
*/
if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Lecturer") {
    header("location: login.php");
    exit();
}

$lecturerUserId = $_SESSION["userId"];

/*
====================================================
IMPORTANT SYSTEM ASSUMPTION
----------------------------------------------------
- assignments.lecturer_id stores users.user_id
- NOT lecturers.lecturer_id
====================================================
*/

/*
====================================================
FETCH ASSIGNMENTS CREATED BY THIS LECTURER
----------------------------------------------------
- Join units to show unit name
- Order newest first
====================================================
*/
$sql = "
    SELECT 
        a.assignment_id,
        a.title,
        a.description,
        a.due_date,
        a.file_path,
        a.created_at,
        u.unit_name
    FROM assignments a
    JOIN units u ON a.unit_id = u.unit_id
    WHERE a.lecturer_id = ?
    ORDER BY a.created_at DESC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $lecturerUserId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<?php require_once "includes/header.php"; ?>

<div class="container mt-5">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Assignments</h2>
        <a href="create-assignment.php" class="btn btn-primary">
            + Create Assignment
        </a>
    </div>

    <!-- ASSIGNMENTS TABLE -->
    <div class="card shadow">
        <div class="card-body">

            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Unit</th>
                        <th>Due Date</th>
                        <th>File</th>
                        <th>Created</th>

                        <!-- ACTIONS COLUMN -->
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (mysqli_num_rows($result) > 0): ?>

                    <?php while ($assignment = mysqli_fetch_assoc($result)): ?>
                        <tr>

                            <!-- TITLE -->
                            <td>
                                <strong>
                                    <?php echo htmlspecialchars($assignment['title']); ?>
                                </strong>
                            </td>

                            <!-- UNIT -->
                            <td>
                                <?php echo htmlspecialchars($assignment['unit_name']); ?>
                            </td>

                            <!-- DUE DATE -->
                            <td>
                                <?php
                                echo $assignment['due_date']
                                    ? htmlspecialchars($assignment['due_date'])
                                    : '—';
                                ?>
                            </td>

                            <!-- FILE -->
         <td>
  <?php if (!empty($assignment['file_path'])): ?>
      <a class="btn btn-sm btn-outline-primary"
         href="<?php echo $BASE_PATH . htmlspecialchars($assignment['file_path']); ?>"
         target="_blank">
          View File
      </a>
  <?php else: ?>
      <span class="text-muted">No file</span>
  <?php endif; ?>
</td>


                            <!-- CREATED DATE -->
                            <td>
                                <?php echo date("Y-m-d", strtotime($assignment['created_at'])); ?>
                            </td>

                            <!-- ACTION BUTTONS -->
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">

                                    <!-- EDIT ASSIGNMENT -->
                                    <a href="edit-assignment.php?id=<?php echo $assignment['assignment_id']; ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>

                                    <!-- DELETE ASSIGNMENT -->
                                    <a href="includes/delete-assignment-inc.php?id=<?php echo $assignment['assignment_id']; ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Are you sure you want to delete this assignment?');">
                                        Delete
                                    </a>

                                </div>
                            </td>

                        </tr>
                    <?php endwhile; ?>

                <?php else: ?>

                    <!-- EMPTY STATE -->
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            No assignments created yet.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>
            </table>

        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>
