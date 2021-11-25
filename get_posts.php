<?php
$user = 'root';
$password = '';
$database = 'posts';

$pdo = new PDO('mysql:host=localhost;dbname=' . $database, $user, $password, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$stmt = $pdo->query('SELECT * FROM `posts`');
$blogs = $stmt->fetchAll();
$stmt = $pdo->query('SELECT * FROM `comments`');
$comments = $stmt->fetchAll();

echo json_encode($blogs);

