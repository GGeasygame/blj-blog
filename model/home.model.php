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

$loggedInID = '';
if (isset($_POST['login'])) {
    $inputUsernameEmail = $_POST['usernameEmail'];
    $inputPassword = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE (username = :username or email = :email) and (user_password = SHA1(:user_password))");
    $stmt->execute([':username' => $inputUsernameEmail, ':email' => $inputUsernameEmail, ':user_password' => $inputPassword]);
    $userValidation = $stmt->fetchAll();


    if (count($userValidation) > 0) {
        $userValidation = $userValidation[0];
        $loggedInID = $userValidation['id'];

        $_SESSION['userdata'][] = $userValidation;
    } else {
        $errors[] = 'Wrong Password or Username';
    }
    
}
if (!empty($_SESSION['userdata'])) {
    $loggedInID = $_SESSION['userdata'][0]['id'];
    $userValidation = $_SESSION['userdata'][0];
}


if (isset($_POST['post-comment'])) {
    if (!empty($_POST['post-comment-text'])) {
        $commentText = $_POST['post-comment-text'];
    } else {
        $errors[] = 'Please enter a comment';
    }
    if (strlen($_POST['post-comment-text']) > 200) {
        $errors[] = 'Your comment is too long';
    } 
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO `comments` (comment_text, created_by, created_at, post_id) VALUES (:commentText, :commentUsername, :postTime, :postID)");
        $stmt->execute(['commentText' => $commentText, 'commentUsername' => $loggedInID, 'postTime' => $postDateTime, 'postID' => $_POST['commentID']]);


        $stmt = $pdo->prepare("SELECT `post_id` FROM `comments` WHERE (comment_id = :id)");
        $stmt->execute(['id' => $pdo->lastInsertId()]);
        $post_id = $stmt->fetchAll()[0]['post_id'];

        $message = ("Hello, \r\nSomeone commented on your blog.\r\n\r\nComment: $commentText");
        sendMailByBlogID($pdo, $post_id, $userValidation, $message);
        header('Location: ' . $_SERVER['PHP_SELF']);
    }
    
}

if (isset($_POST['rep'])) {
    $rep = $_POST['rep'];
    $repID = $_POST['repID'];

    if ($_POST['repPostLike'] === '') {$_POST['repPostLike'] = 0;}
    if ($_POST['repPostDislike'] === '') {$_POST['repPostDislike'] = 0;}
    if ($rep === 'like') {
        
        $postLike = $_POST['repPostLike'] + 1;

        $stmt = $pdo->prepare("UPDATE `posts` SET like_post = $postLike WHERE id = :repID");
        $stmt->execute(['repID' => $repID]);
    } elseif ($rep === 'dislike') {
        $postDislike = $_POST['repPostDislike'] + 1;
        $stmt = $pdo->prepare("UPDATE `posts` SET dislike_post = :postDislike WHERE id = :repID");
        $stmt->execute([':postDislike' => $postDislike, ':repID' => $repID]);
    }
    
    if ($rep === 'like' || $rep === 'dislike') {
        if($rep === 'like'){ $message = ("Hello, someone liked your blog.");}
        if($rep === 'dislike'){ $message = ("Hello, someone disliked your blog.");}
        sendMailByBlogID($pdo, $repID, $userValidation, $message);

        header('Location: ' . $_SERVER['PHP_SELF']);
    }
}

function sendMailByBlogID($pdo, $blogID, $userValidation, $message) {
    $stmt = $pdo->prepare("SELECT `created_by` FROM `posts` WHERE (id = :id)");
    $stmt->execute(['id' => $blogID]);
    $postCreator = $stmt->fetchAll()[0]['created_by'];


    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE (id = :id)");
    $stmt->execute(['id' => $postCreator]);
    $postCreatorData = $stmt->fetchAll()[0];


    $to = $postCreatorData['email'];
    $subject = "Jonas Blog";

    if (mail($to, $subject, $message)) {
        echo 'email sent comment';
    }
}


if (isset($_POST['post-blog'])) {
    if (!empty($_POST['post-text'])) {
        $postText = $_POST['post-text'];
    } else {
        $errors[] = 'Please enter a text';
    }
    if(strlen($_POST['post-text']) > 230) {
        $errors[] = 'Youre post is too long';
    }
    if (!empty($_POST['post-title'])) {
        $postTitle = $_POST['post-title'];
    } else {
        $errors[] = 'Please enter a title';
    }
    if(strlen($_POST['post-title']) > 20) {
        $errors[] = 'Your title is too long';
    }

    if (!empty($_POST['img']) && @!is_array(getimagesize($_POST['img']))) {
        $errors[] = 'Please enter valid Image-URL';
    }
    if (!empty($_POST['img']) && strlen($_POST['img'] > 200)) {
        $errors[] = 'The image-URL is too long';
    }
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO `posts` (created_by, created_at, post_title, post_text, img_url) VALUES (:username, :postTime, :postTitle, :postText, :img)");
        
        $imagesString = $_POST['img'];
        
        unset($_SESSION['img']);
        $stmt->execute([':username' => $loggedInID, 'postTime' => $postDateTime, 'postTitle' => $postTitle, 'postText' => $postText, 'img' => $imagesString]);
        
        header('Location: ' . $_SERVER['PHP_SELF']);

    }
}

if (isset($_SESSION['userdata']) && isset($_POST['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
}


?>