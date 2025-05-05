<?php include('connect_db.php'); ?>
<link rel="stylesheet" type="text/css" href="css/log_in.css">
<div id="overlay">
    <div class="Authentication-Form-Dad">
        <form id="Authentication-Form" method="post">
            <label for="staffname" class="signin">Username:</label><br>
            <input type="text" id="staffname" name="staffname" placeholder="Username"><br>
            <label for="password" class="signin">Password:</label><br>
            <input type="password" id="password" name="password" placeholder="Password"><br>
            <input type="submit" class="signin" value="Sign In">
        </form>
    </div>
</div>
