<?php

$id = $_POST['userID'];

$user = 'root';
$password = '';
$database = 'posts';

$pdo = new PDO('mysql:host=localhost;dbname=' . $database, $user, $password, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$stmt = $pdo->query("SELECT * FROM `users` WHERE id=$id");
$userData = $stmt->fetchAll();
 

if (isset($_POST['new-password-submit'])) {
    $newPassword = $_POST['new-password'];
    $oldPassword = $_POST['old-password'];
    $userID = $_POST['userID'];
    var_dump($userID);

    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE user_password = SHA1(:oldPassword) and id = :id");
    $stmt->execute([':oldPassword' => $oldPassword, ':id' => $userID]);
    $userValidation = $stmt->fetchAll();

    if (count($userValidation) > 0) {
        $stmt = $pdo->prepare("UPDATE `users` SET user_password = SHA1(:newPassword) WHERE id = :id");
        $stmt->execute([':newPassword' => $newPassword, 'id' => $userID]);
        echo 'SUCCESS!';
    } else {
        echo 'wrong password';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="changePassword.css">
</head>
<body>
    <fieldset>
        <legend>Change Password</legend>
        <form action="" method="post">
            <label for="old-password">Enter Old Password </label>
            <input type="password" name="old-password" class="old-password">
            <label for="new-password">Enter New Password</label>
            <input type="password" name="new-password" class="new-password">
            <input type="submit" value="submit" name="new-password-submit" class="new-password-submit">

            <input type="hidden" name="userID" value=<?=$id?>>
        </form>



    </fieldset>
</body>
</html>