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


date_default_timezone_set('Europe/Zurich');
$postDateTime = date("d.m.Y H:i:s", time());
$errors = array();
if (isset($_POST['post-blog'])) {
    if (!empty($_POST['username'])) {
        $username = $_POST['username'];
    } else {
        $errors[] = 'Please enter a username';
    }
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
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO `posts` (created_by, created_at, post_title, post_text) VALUES (:username, :postTime, :postTitle, :postText)");
        $stmt->execute([':username' => $username, 'postTime' => $postDateTime, 'postTitle' => $postTitle, 'postText' => $postText]);
    }
}
/*
if (isset($_POST['post-comment'])) {
    if (!empty($_POST['comment-username'])) {
        $commentUsername = $_POST['comment-username'];
    } else {
        $errors[] = 'Please enter a username';
    }
    if (!empty($_POST['post-comment-text'])) {
        $commentText = $_POST['post-comment-text'];
    } else {
        $errors[] = 'Please enter a comment';
    }
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO `comments` (comment_text, created_by, created_at, post_id) VALUES (:commentText, :commentUsername, :postTime, :postID)");
        $stmt->execute(['commentText' => $commentText, 'commentUsername' => $commentUsername, 'postTime' => $postDateTime, 'postID' => .....]);
    }
}
*/
echo 'commit';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
    <div class="grid">
        <section class="header element">
            <h1>Blog</h1>
        </section>

        <section class="body-blogger element">
            <?php if (!empty($errors)) { ?>
                <div class="error-box">
                    <ul>
                        <?php foreach($errors as $error) { ?>
                            <li><?=$error?></li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
           
            <form action="./blog.php" method="post" class="post-form">
                <label for="username">Name: </label>
                <input type="text" class="username" name="username"></input>
                <label for="title">Title: </label>
                <input type="text" class="post-title" name="post-title"></input>
                <textarea name="post-text" class="post-text" cols="100" rows="13" placeholder="Enter your text here"></textarea>
                <input type="submit" id="post-blog" value="submit" name="post-blog">
            </form>

        </section>

        <section class="body-user element">

            <div class="flex-container">
                
                <?php foreach($blogs as $blog) { ?>
                    <div class="flex-element">
                        <h2 class="post_title"><?=htmlspecialchars($blog['post_title'])?></h1>
                        <h2 class="created_by"><?=htmlspecialchars($blog['created_by'])?></h2>
                        <h2 class="created_at"><?=htmlspecialchars($blog['created_at'])?></h3>
                        <p class="post_text"><?=htmlspecialchars($blog['post_text'])?></p>
                        <div class="post-comment">
                            <form action="./blog.php" method="post" class="post-comment-form">
                                <label for="comment-username">Name: </label>
                                <input type="text" class="comment-username" name="comment-username"></input>
                                <textarea name="post-comment-text" id="post-comment-text" cols="30" rows="1" placeholder="Enter your text here"></textarea>
                                <input type="submit" id="post-comment" value="submit" name="post-comment">
                            </form>
                        </div>
                    </div>
                <?php } ?>    
            </div>
        </section>

    </div>
</body>
</html>