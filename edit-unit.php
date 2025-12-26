
<?php
require_once "includes/header.php"; // ← здесь session_start()

// теперь сессия уже существует
if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Admin") {
    header("location: login.php");
    exit();
}

require_once "includes/dbh.php";



// Security check
if (!isset($_SESSION["userId"]) || $_SESSION["role"] !== "Admin") {
    header("location: login.php");
    exit();
}

// Check if ID exists
if (!isset($_GET["id"])) {
    header("location: admin-courses.php");
    exit();
}

$unitId = (int) $_GET["id"];

// Fetch unit data
$sql = "SELECT unit_name, unit_description FROM units WHERE unit_id = ?";
$stmt = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt, $sql)) {
    die("SQL error");
}

mysqli_stmt_bind_param($stmt, "i", $unitId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$unit = mysqli_fetch_assoc($result);

if (!$unit) {
    header("location: admin-courses.php");
    exit();
}
?>




<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Unit</h5>
                </div>

                <div class="card-body">
                    <form action="includes/edit-unit-inc.php" method="post">
                        <input type="hidden" name="unit_id" value="<?php echo $unitId; ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Unit Name</label>
                            <input type="text"
                                   name="unit_name"
                                   class="form-control"
                                   value="<?php echo htmlspecialchars($unit['unit_name']); ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="unit_description"
                                      class="form-control"
                                      rows="3"><?php echo htmlspecialchars($unit['unit_description']); ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="admin-courses.php" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
