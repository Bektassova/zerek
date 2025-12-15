<?php include "includes/header.php"; ?>

<div class="container" style="width:800px;">
    <div class="row">
        <div class="col"><h3>Register</h3></div>
    </div>
    <div class="row">
        <div class="col">
            <form action="includes/register-inc.php" method="post">
                <div class="row">
                    <div class="col">
                        <input type="text" name="username" placeholder="Username" class="w-100 m-2" required>
                    </div>
                    <div class="col">
                        <input type="email" name="email" placeholder="Email" class="w-100 m-2" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="password" name="password" placeholder="Password" class="w-100 m-2" required>
                    </div>
                    <div class="col">
                        <input type="password" name="confpass" placeholder="Repeat Password" class="w-100 m-2" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="text" name="name" placeholder="First Name" class="w-100 m-2" required>
                    </div>
                    <div class="col">
                        <input type="text" name="surname" placeholder="Surname" class="w-100 m-2" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="role" class="ms-2">Role:</label>
                        <select name="role" id="role" class="w-100 m-2 p-1">
                            <option value="Student">Student</option>
                            <option value="Lecturer">Lecturer</option>
                            <option value="Parents">Parent</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                    <div class="col p-2">
                        <label for="dob" class="ms-2">Date of Birth:</label>
                        <input type="date" name="dob" id="dob" class="w-100 m-2" required>
                    </div>
                </div>
                <div class="row my-3">
                    <div class="col">
                        <button class="btn btn-success w-100 m-2" type="submit" name="submit">Submit</button>
                    </div>
                    <div class="col">
                        <button class="btn btn-danger w-100 m-2" type="reset">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php 
        if(isset($_GET["error"])) { 
            echo '<div class="alert alert-danger">Error: Registration failed. Check your inputs.</div>';
        }
        if(isset($_GET["success"])) { 
            echo '<div class="alert alert-success">Registration successful!</div>';
        }
    ?>
</div>

<?php include "includes/footer.php"; ?>