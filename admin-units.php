<?php
require_once "includes/header.php";

// Security check — ONLY ADMIN
if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Admin") {
    header("location: login.php");
    exit();
}

require_once "includes/dbh.php";

// Fetch units with course name
$sql = "
    SELECT 
        u.unit_id,
        u.unit_name,
        u.unit_description,
        c.course_name
    FROM units u
    LEFT JOIN courses c ON u.course_id = c.course_id
    ORDER BY u.unit_name ASC
";
$result = mysqli_query($conn, $sql);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Unit Management</h2>
        <a href="admin-dashboard.php" class="btn btn-primary">Back to Admin Control Panel</a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Unit</th>
                        <th>Course</th>
                        <th>Description</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($unit = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($unit['unit_name']); ?></td>
                            <td><?php echo htmlspecialchars($unit['course_name'] ?? 'Not linked'); ?></td>
                            <td><?php echo htmlspecialchars($unit['unit_description'] ?? '—'); ?></td>
                            <td class="text-nowrap text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="edit-unit.php?id=<?php echo $unit['unit_id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    
                                   <a href="includes/delete-unit-inc.php?id=<?php echo $unit['unit_id']; ?>&return=admin-units.php"
   class="btn btn-sm btn-outline-danger"
   onclick="return confirm('Delete this unit?')">
   Delete
</a>

                                    <a href="admin-unit-enroll.php?unit_id=<?php echo $unit['unit_id']; ?>" class="btn btn-sm btn-outline-success">Enroll Students</a>

                                    <a href="admin-assign-lecturer.php?unit_id=<?php echo $unit['unit_id']; ?>" class="btn btn-sm btn-outline-info">Assign Lecturers</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">No units found</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>
