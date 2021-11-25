<?php

$user = 'root';
$password = '';
$database = 'posts';

$pdo = new PDO('mysql:host=localhost;dbname=' . $database, $user, $password, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);




$errors = array();

if (isset($_POST['submit'])) {
    if ($_POST['first-name'] === '' || $_POST['last-name'] === '' || $_POST['email'] === '' || $_POST['username'] === '' || $_POST['password'] === '') {
        $errors[] = 'Please fill all boxes';
    } 
    if ($_POST['password'] !== $_POST['repeat-password']) {
        $errors[] = 'The passwords must be the same';
    }
    if (!preg_match("/^([a-zA-Z' ]+)$/",$_POST['first-name']) || !preg_match("/^([a-zA-Z' ]+)$/",$_POST['last-name'])) {
        $errors[] = 'Please enter valid name';
    }
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO `users` (first_name, last_name, email, username, user_password) VALUES (:firstName, :lastName, :email, :username, SHA1(:password))");
        $stmt->execute([':firstName' => $_POST['first-name'], ':lastName' => $_POST['last-name'], ':email' => $_POST['email'], ':username' => $_POST['username'], ':password' => $_POST['password']]);
    }
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

    <?php if (!empty($errors)) { ?>
        <div class="error-box">
            <ul>
                <?php foreach($errors as $error) { ?>
                    <li><?=$error?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

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
            <div>
                <label for="password">Password </label>
                <input type="password" name="password" class="password">
            </div>
            <div>
                <label for="repeat-password">Repeat Password </label>
                <input type="password" name="repeat-password" class="repeat-password">
            </div>
            <input type="submit" value="submit" name="submit" class="submit">
        </form>


    </fieldset>
</body>
</html>