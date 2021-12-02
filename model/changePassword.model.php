<?php

$id = $_POST['userID'];

$pdo = new PDO('mysql:host=mysql2.webland.ch;dbname=d041e_jokaufmann', 'd041e_jokaufmann', '54321_Db!!!', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$stmt = $pdo->query("SELECT * FROM `users` WHERE id=$id");
$userData = $stmt->fetchAll();
 
$logged = 0;
if (isset($_POST['new-password-submit'])) {
    $newPassword = $_POST['new-password'];
    $oldPassword = $_POST['old-password'];
    $userID = $_POST['userID'];

    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE user_password = SHA1(:oldPassword) and id = :id");
    $stmt->execute([':oldPassword' => $oldPassword, ':id' => $userID]);
    $userValidation = $stmt->fetchAll();

    if (count($userValidation) > 0) {
        $stmt = $pdo->prepare("UPDATE `users` SET user_password = SHA1(:newPassword) WHERE id = :id");
        $stmt->execute([':newPassword' => $newPassword, 'id' => $userID]);
        $logged = 2;
    } else {
        $logged = 1;
    }
}

?>