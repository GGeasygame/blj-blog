<?php include './model/home.model.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <link rel="stylesheet" href="./view/home/stylesheet.css">
</head>
<body>
    <div class="grid">
        <section class="header element">
            <h1>Jonas' Blog</h1>

            <?php if ($loggedInID === '') { ?>
                <form action="" method="post" class="loginForm">
                    <fieldset class="loginFieldset">
                    <legend>Login</legend>
                    <label for="usernameEmail">Enter Username or Email</label>
                    <input type="text" name="usernameEmail" class="usernameEmail" placeholder="username/email">
                    <label for="password">Enter Password</label>
                    <input type="password" name="password" class="password">
                    <input type="submit" value="Login" name="login" class="login">

                    <a href="index.php?page=register">No Account Yet? Register Here</a>
                    </fieldset>
                </form>
            <?php } else { ?>
                <fieldset class="loggedInUser">
                    <legend>User</legend>
                <h2 class="loggedin-text">You are Logged in as <?=$userValidation['username']?></h2>
                <form action="index.php?page=changePassword" method="post">
                    <input type="submit" value="Change Password" id="change-password" name="change-password">

                    <input type="hidden" name="userID" value=<?=$userValidation['id']?>>
                </form>
                <form action="" method="post">
                    <input type="submit" value="logout" name="logout" class="logout">
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
           
            <?php if ($loggedInID === '') { ?>
                <h2 class="create-account-message">Create Account To Post</h2>
            <?php } else {?>
            <form action="" method="post" class="post-form">
                <label for="title">Title: </label>
                <input type="text" class="post-title" name="post-title"></input>
                <textarea name="post-text" class="post-text" cols="100" rows="13" placeholder="Enter your text here"></textarea>
                <label for="img">Insert Image-URL: </label>
                <input type="text" class="img" name="img"></input><br>
                <input type="submit" id="post-blog" value="submit" name="post-blog">
            </form>
            <?php } ?>
        </section>

        <section class="body-user element">

            <div class="flex-container">
                
                <?php 
                foreach($blogs as $blog) { 
                    $stmt = $pdo->prepare("SELECT `username` FROM `users` WHERE id = :id");
                        $stmt->execute(['id' => $blog['created_by']]);
                        $username = $stmt->fetchAll();
                        $username =$username[0]['username'];
                    ?>
                    <div class="flex-post">
                        <h2 class="post_title"><?=htmlspecialchars($blog['post_title'])?></h1>
                        <h2 class="created_by"><?php 
                        echo htmlspecialchars($username);
                        ?></h2>
                        <h2 class="created_at"><?=htmlspecialchars($blog['created_at'])?></h3>
                        <p class="post_text"><?=htmlspecialchars($blog['post_text'])?></p>

                        <div class="img-flex">
                            <?php if ($blog['img_url'] != null) { 
                                $images = $blog['img_url'];
                               ?>
                                    <img src="<?=htmlspecialchars($images)?>" alt="image">
                                    
                                    <?php
                                } ?>
                        </div>

                        <div class="post-comment">
                            <?php if ($loggedInID !== '') { ?>
                            <form action="" method="post" class="post-comment-form" class="comment-form">
                                <textarea name="post-comment-text" id="post-comment-text" cols="30" rows="1" placeholder="Enter your text here"></textarea>
                                <input type="submit" id="post-comment" value="submit" name="post-comment">

                                <input type="hidden" name="commentID" value=<?=$blog['id']?> />
                            </form>
                            <?php } else { ?>
                                <p>Log in to comment</p>

                            <?php } ?>

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
                            <?php foreach ($comments as $comment) { 
                                $stmt = $pdo->prepare("SELECT `username` FROM `users` WHERE id = :id");
                                $stmt->execute(['id' => $comment['created_by']]);
                                $username = $stmt->fetchAll();
                                $username = $username[0]['username'];
                                ?>
                                
                                <?php if ($comment['post_id'] == $blog['id']) { ?>
                                <div class="comment">
                                    <p class="created_by"><?=htmlspecialchars($username)?></p>
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