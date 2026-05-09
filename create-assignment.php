<?php
session_start();
require_once "includes/dbh.php";

// 1. УНИВЕРСАЛЬНАЯ ПРОВЕРКА
if (!isset($_SESSION["userId"]) || ($_SESSION["role"] !== "Lecturer" && $_SESSION["role"] !== "Teacher")) {
    header("location: login.php?error=notauthorized");
    exit();
}

$lecturerUserId = $_SESSION["userId"];

// ========================================================
// 2. ЛОГИКА СОХРАНЕНИЯ (ДОБАВЛЯЕМ ЭТОТ БЛОК!)
// ========================================================
if (isset($_POST["create_assignment"])) {
    
    $unit_id = $_POST['unit_id'];
    $task_number = (int)$_POST['task_number']; // Наш новый номер таска
    $title = $_POST['title'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];

    // SQL запрос (убедись, что ты добавила task_number в таблицу assignments через phpMyAdmin!)
    $sqlInsert = "INSERT INTO assignments (unit_id, task_number, title, description, deadline, created_by) 
                  VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmtInsert = mysqli_prepare($conn, $sqlInsert);
    
    // Привязываем параметры: i (int), i (int), s (string), s (string), s (string), i (int)
    mysqli_stmt_bind_param($stmtInsert, "iisssi", $unit_id, $task_number, $title, $description, $deadline, $lecturerUserId);

    if (mysqli_stmt_execute($stmtInsert)) {
        // Если всё успешно, отправляем на список заданий
        header("location: lecturer-assignments.php?upload=success");
        exit();
    } else {
        $error_message = "Database error: " . mysqli_error($conn);
    }
}
// ========================================================

// 3. ТВОЙ СТАРЫЙ КОД ПОЛУЧЕНИЯ ЮНИТОВ (ОСТАВЛЯЕМ КАК ЕСТЬ)
$sqlUnits = "
    SELECT u.unit_id, u.unit_name
    FROM units u
    INNER JOIN lecturer_units lu ON u.unit_id = lu.unit_id
    WHERE lu.lecturer_id = ?
    ORDER BY u.unit_name ASC
";
$stmt = mysqli_prepare($conn, $sqlUnits);
mysqli_stmt_bind_param($stmt, "i", $lecturerUserId);
mysqli_stmt_execute($stmt);
$unitsResult = mysqli_stmt_get_result($stmt);

require_once "includes/header.php"; 
?>

<div class="container mt-5">
    <h2 class="mb-4">Create Assignment</h2>

    <div class="card shadow">
        <div class="card-body">

            <form action="includes/create-assignment-inc.php" method="post" enctype="multipart/form-data">

                <!-- UNIT -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Unit</label>
                    <select name="unit_id" class="form-select" required>
                        <option value="">-- Select Unit --</option>
                        <?php while ($unit = mysqli_fetch_assoc($unitsResult)): ?>
                            <option value="<?php echo $unit['unit_id']; ?>">
                                <?php echo htmlspecialchars($unit['unit_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
    <label> Select Task Number (1-4):</label>
    <select name="task_number" class="form-select" required>
        <option value="1">Task 1</option>
        <option value="2">Task 2</option>
        <option value="3">Task 3</option>
        <option value="4">Task 4</option>
    </select>
</div>

                <!-- TITLE -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Assignment Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <!-- DESCRIPTION -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea name="description" class="form-control" rows="4"></textarea>
                </div>

                <!-- DUE DATE -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Due Date</label>
                    <input type="date" name="due_date" class="form-control">
                </div>

                <!-- FILE -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Assignment File</label>
                    <input type="file" name="assignment_file" class="form-control">
                    <small class="text-muted">
                        Allowed: PDF, DOC, DOCX
                    </small>
                </div>

                <button type="submit" name="create_assignment" class="btn btn-success">
                    Create Assignment
                </button>

                <a href="lecturer-assignments.php" class="btn btn-secondary ms-2">
                    Cancel
                </a>

            </form>
    <a href="profile.php" class="btn btn-secondary mt-3">Back to Profile</a>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>
