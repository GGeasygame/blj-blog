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
if (isset($_POST['submit-img'])) {
    if (empty($_POST['img'])) {
        $errors[] = 'Please enter Image-URL';
    } else if (@!is_array(getimagesize($_POST['img']))) {
        $errors[] = 'Please enter valid Image-URL';
    } else {
        $_SESSION['img'][] = $_POST['img'];
        $imagesArray = $_SESSION;
        if (sizeof($imagesArray)>1) {
            array_pop($imagesArray);
        }
    }
}

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
        $stmt = $pdo->prepare("INSERT INTO `posts` (created_by, created_at, post_title, post_text, img_url) VALUES (:username, :postTime, :postTitle, :postText, :img)");
        
        foreach($imagesArray['img'] as $img) {
            $imagesString = $imagesString . $img . ';;;;';
        }
        
        unset($_SESSION['img']);
        $stmt->execute([':username' => $username, 'postTime' => $postDateTime, 'postTitle' => $postTitle, 'postText' => $postText, 'img' => $imagesString]);
    }
}

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
        $stmt->execute(['commentText' => $commentText, 'commentUsername' => $commentUsername, 'postTime' => $postDateTime, 'postID' => $_POST['commentID']]);
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
    } else {
        $errors[] = 'Wrong Password or Username';
    }
    
}

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
            <h1>Jonas' Blog</h1>

            <?php if ($loggedInUsername === '') { ?>
                <form action="" method="post" class="loginForm">
                    <fieldset class="loginFieldset">
                    <legend>Login</legend>
                    <label for="usernameEmail">Enter Username or Email</label>
                    <input type="text" name="usernameEmail" class="usernameEmail" placeholder="username/email">
                    <label for="password">Enter Password</label>
                    <input type="password" name="password" class="password">
                    <input type="submit" value="Login" name="login" class="login">

                    <a href="./register.php">No Account Yet? Register Here</a>
                    </fieldset>
                </form>
            <?php } else { ?>
                <fieldset class="loggedInUser">
                    <legend>User</legend>
                <h2 class="loggedin-text">You are Logged in as <?=$loggedInUsername?></h2>
                <form action="./changePassword.php" method="post">
                    <input type="submit" value="Change Password" id="change-password" name="change-password">

                    <input type="hidden" name="userID" value=<?=$userValidation['id']?>>
                </form>
                </fieldset>
            <?php } ?>
        </section>

        <section class="navigation">
            <ul>

                <?php foreach ($urls as $url) { ?>

                    <li><a href=<?=$url['url']?>><?=$url['description']?></a></li>

                <?php } ?>


            </ul>
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
            <form action="" method="post">
                <label for="img">Insert Image-URL: </label>
                <input type="text" class="img" name="img"></input>
                <input type="submit" id="submit-img" value="submit" name="submit-img">
            </form>

        </section>

        <section class="body-user element">

            <div class="flex-container">
                
                <?php 
                foreach($blogs as $blog) { ?>
                    <div class="flex-post">
                        <h2 class="post_title"><?=htmlspecialchars($blog['post_title'])?></h1>
                        <h2 class="created_by"><?=htmlspecialchars($blog['created_by'])?></h2>
                        <h2 class="created_at"><?=htmlspecialchars($blog['created_at'])?></h3>
                        <p class="post_text"><?=htmlspecialchars($blog['post_text'])?></p>

                        <div class="img-flex">
                            <?php if ($blog['img_url'] != null) { 
                                $images = explode(';;;;', $blog['img_url']);
                                array_pop($images);
                                foreach ($images as $image) { ?>
                                    <img src="<?=htmlspecialchars($image)?>" alt="image">
                                    
                                    <?php }
                                } ?>
                        </div>

                        <div class="post-comment">
                            <form action="./blog.php" method="post" class="post-comment-form" class="comment-form">
                                <label for="comment-username">Name: </label>
                                <input type="text" class="comment-username" name="comment-username"></input>
                                <textarea name="post-comment-text" id="post-comment-text" cols="30" rows="1" placeholder="Enter your text here"></textarea>
                                <input type="submit" id="post-comment" value="submit" name="post-comment">

                                <input type="hidden" name="commentID" value=<?=$blog['id']?> />
                            </form>

                            <form action="" method="post" class="rep-form">         
                                <input type="radio" id="like" name="rep" value="like">
                                <label for="like">Like </label>
                                <input type="radio" id="dislike" name="rep" value="dislike">
                                <label for="dislike">Dislike </label>
                                <input type="submit" value="submit" name="submit-rep" id="submit-rep">
                            
                                <input type="hidden" name="repID" value=<?=$blog['id']?>>
                                <input type="hidden" name="repPostLike" value=<?=$blog['like_post']?>>
                                <input type="hidden" name="repPostDislike" value=<?=$blog['dislike_post']?>>
                            </form>
                            <p>Likes: <?=$blog['like_post'] === null ? 0 : $blog['like_post']?> Dislikes: <?=$blog['dislike_post'] === null ? 0 : $blog['dislike_post']?></p>
                        </div>
                        <div class="comments">
                            <?php foreach ($comments as $comment) { ?>
                                
                                <?php if ($comment['post_id'] == $blog['id']) { ?>
                                <div class="comment">
                                    <p class="created_by"><?=htmlspecialchars($comment['created_by'])?></p>
                                    <p class="created_at"><?=htmlspecialchars($comment['created_at'])?></p>
                                    <p class="comment_text"><?=htmlspecialchars($comment['comment_text'])?></p>
                                </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>    
            </div>
        </section>

        <section class="other-blogs">
            
        </section>

    </div>
</body>
</html>