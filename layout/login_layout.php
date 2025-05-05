<!-- change login/logut -->
<?php
    if (isset($_GET['mode'])){
        switch ($_GET['mode']){
            case 'sign-in':?>
<label for="username">Username:</label><br>
<input type="text" id="username" name="username" placeholder="Username"><br>
<label for="password">Password:</label><br>
<input type="password" id="password" name="password" placeholder="Password"><br>
<input type="submit" value="Sign In">
<?php break;
 case 'sign-up':?>
<label for="name">Name:</label><br>
<input type="text" id="name" name="name" placeholder="Name"><br>
<label for="username">Username:</label><br>
<input type="text" id="username" name="username" placeholder="Username"><br>
<label for="password">Password:</label><br>
<input type="password" id="password" name="password" placeholder="Password"><br>
<label for="email">Email:</label><br>
<input type="text" id="email" name="email" placeholder="Email"><br>
<label for="phone_number">Phone:</label><br>
<input type="tel" id="phone_number" name="phone_number" placeholder="Phone"><br>
<input type="submit" value="Sign Up">
<?php break;
        }
    }