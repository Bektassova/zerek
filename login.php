<?php include "includes/header.php"; ?>

<div class="container" style="width:400px; margin-top: 100px;">
    <h3 class="text-center">LOGIN</h3>
    <form action="includes/login-inc.php" method="post">
        <div class="mb-3">
            <input type="text" name="uid" placeholder="Username or Email" class="form-control" required>
        </div>
        <div class="mb-3">
            <input type="password" name="pwd" placeholder="Password" class="form-control" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <?php
    if (isset($_GET["error"])) {
        if ($_GET["error"] == "incorrectlogin") {
            echo "<p class='text-danger mt-3'>Invalid login details!</p>";
        }
    }
    ?>
</div>

<?php include "includes/footer.php"; ?>