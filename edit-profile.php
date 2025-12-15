<?php 
    include "includes/header.php";

?>

<div class="container" style="width:800px;">
    <div class="row">
        <div class="col">
            <h3>Edit Profile</h3>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <form action="" method="post">
                <div class="row">
                    <div class="col">
                        <input type="text" name="username" id="username" placeholder="Username" class="w-100 m-2">
                    </div>
                    <div class="col">
                        <input type="password" name="password" id="password" placeholder="Password" class="w-100 m-2">
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="text" name="name" id="name" placeholder="First Name" class="w-100 m-2">
                    </div>
                    <div class="col">
                        <input type="text" name="surname" id="surname" placeholder="Last Name" class="w-100 m-2">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col w-100 m-2">
                        <label for="age" class="float-end">Age:</label>
                    </div>
                    <div class="col w-100 m-2">
                        <select name="age" id="age">
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                            <option value="20">20</option>
                            <option value="21">21</option>
                            <option value="22">22</option>
                            <option value="23">23</option>
                            <option value="24">24</option>
                            <option value="25">25</option>
                        </select>
                    </div>
                </div>
                <div class="row my-3">
                    <div class="col">
                        <button class="btn btn-success w-100 m-2" type="submit">Submit</button>
                    </div>
                    <div class="col">
                        <button class="btn btn-danger w-100 m-2" type="reset">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <form action="" method="post">
                <button type="submit" id="deleteAccount" name="deleteAccount">Delete Account</button>
            </form>
        </div>
    </div>
</div>




<?php include "includes/footer.php"; ?>