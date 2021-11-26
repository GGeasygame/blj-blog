<?php


$pdo2 = new PDO('mysql:host=mysql2.webland.ch;dbname=d041e_dagomez', 'd041e_dagomez', '54321_Db!!!', [
    PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_INIT_COMMAND    => 'SET NAMES utf8',
]);
$stmt = $pdo2->query('SELECT `url`, `description` FROM `urls`');
$urls = $stmt->fetchAll();
array_multisort(array_column($urls, 'description'), SORT_ASC, $urls);


$ownIP = $_SERVER['SERVER_NAME'];
echo $ownIP;
$path = $ownIP . '/uek-betrieb_php/blog/get_posts.php';
$jsonString = file_get_contents($path);
json_decode($jsonString);

