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


$pdo2 = new PDO('mysql:host=mysql2.webland.ch;dbname=d041e_dagomez', 'd041e_dagomez', '54321_Db!!!', [
    PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_INIT_COMMAND    => 'SET NAMES utf8',
]);
$stmt = $pdo2->query('SELECT `url`, `description` FROM `urls`');
$urls = $stmt->fetchAll();
array_multisort(array_column($urls, 'description'), SORT_ASC, $urls);

session_start();
$imagesString = '';
$imagesArray = array();
date_default_timezone_set('Europe/Zurich');
$postDateTime = date("d.m.Y H:i:s", time());
$errors = array();

$imagesArray = $_SESSION;

$loggedInUsername = '';
if (isset($_POST['login'])) {
    $inputUsernameEmail = $_POST['usernameEmail'];
    $inputPassword = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE (username = :username or email = :email) and (user_password = SHA1(:user_password))");
    $stmt->execute([':username' => $inputUsernameEmail, ':email' => $inputUsernameEmail, ':user_password' => $inputPassword]);
    $userValidation = $stmt->fetchAll();


    if (count($userValidation) > 0) {
        $userValidation = $userValidation[0];
        $loggedInUsername = $userValidation['username'];

        $_SESSION['userdata'][] = $userValidation;
    } else {
        $errors[] = 'Wrong Password or Username';
    }
    
}
if (!empty($_SESSION['userdata'])) {
    $loggedInUsername = $_SESSION['userdata'][0]['username'];
    $userValidation = $_SESSION['userdata'][0];
}


if (isset($_POST['post-comment'])) {
    if (!empty($_POST['post-comment-text'])) {
        $commentText = $_POST['post-comment-text'];
    } else {
        $errors[] = 'Please enter a comment';
    }
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO `comments` (comment_text, created_by, created_at, post_id) VALUES (:commentText, :commentUsername, :postTime, :postID)");
        $stmt->execute(['commentText' => $commentText, 'commentUsername' => $loggedInUsername, 'postTime' => $postDateTime, 'postID' => $_POST['commentID']]);
    }
}

if (isset($_POST['rep'])) {
    $rep = $_POST['rep'];
    $repID = $_POST['repID'];

    if ($_POST['repPostLike'] === '') {$_POST['repPostLike'] = 0;}
    if ($_POST['repPostDislike'] === '') {$_POST['repPostDislike'] = 0;}
    if ($rep === 'like') {
        
        $postLike = $_POST['repPostLike'] + 1;

        $pdo->exec("UPDATE `posts` SET like_post = $postLike WHERE id = $repID");
    } elseif ($rep === 'dislike') {
        $postDislike = $_POST['repPostDislike'] + 1;
        $pdo->exec("UPDATE `posts` SET dislike_post = $postDislike WHERE id = $repID");
    }
    
}


if (isset($_POST['post-blog'])) {
    if (!empty($_POST['post-text'])) {
        $postText = $_POST['post-text'];
    } else {
        $errors[] = 'Please enter a text';
    }
    if (!empty($_POST['post-title'])) {
        $postTitle = $_POST['post-title'];
    } else {
        $errors[] = 'Please enter a title';
    }

    if (!empty($_POST['img']) && @!is_array(getimagesize($_POST['img']))) {
        $errors[] = 'Please enter valid Image-URL';
    } 
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO `posts` (created_by, created_at, post_title, post_text, img_url) VALUES (:username, :postTime, :postTitle, :postText, :img)");
        
        $imagesString = $_POST['img'];
        
        unset($_SESSION['img']);
        $stmt->execute([':username' => $loggedInUsername, 'postTime' => $postDateTime, 'postTitle' => $postTitle, 'postText' => $postText, 'img' => $imagesString]);

    }
}

if (isset($_SESSION['userdata']) && isset($_POST['logout'])) {
    session_destroy();
}
?>