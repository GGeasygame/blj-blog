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
        header('Location: index.php?page=home');
    }
}

?>