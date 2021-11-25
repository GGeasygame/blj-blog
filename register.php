<?php

if (isset($_POST['submit'])) {
    
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <fieldset class="register-fieldset">
        <legend>Register</legend>
        <form action="" method="post">
            <div>
                <label for="first-name">First Name </label>
                <input type="name" name="first-name" class="first-name" placeholder="First Name">
            </div>
            <div>
                <label for="last-name">Last Name </label>
                <input type="name" name="last-name" class="last-name" placeholder="Last Name">
            </div>
            <div>
                <label for="email">Email </label>
                <input type="email" name="email" class="email" placeholder="Email">
            </div>
            <div>
                <label for="username">Username </label>
                <input type="text" name="username" class="username" placeholder="Username">
            </div>
            <input type="submit" value="submit" name="submit" class="submit">
        </form>


    </fieldset>
</body>
</html>