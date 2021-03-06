<?php include './model/register.model.php' ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="./view/register/register.css">
</head>
<body>

    <?php if (!empty($errors)) { ?>
        <div class="error-box">
            <ul>
                <?php foreach($errors as $error) { ?>
                    <li><?=$error?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
    
    <h2>Register</h2>
    <fieldset class="register-fieldset">
        <form action="" method="post">
            <div class="flex-align">
                <label for="first-name">First Name </label>
                <input type="name" name="first-name" class="first-name" placeholder="First Name" value="<?php echo ( isset( $_POST['first-name'] ) ? htmlspecialchars($_POST['first-name']) : '' ); ?>">
            </div>
            <div class="flex-align">
                <label for="last-name">Last Name </label>
                <input type="name" name="last-name" class="last-name" placeholder="Last Name" value="<?php echo ( isset( $_POST['last-name'] ) ? htmlspecialchars($_POST['last-name']) : '' ); ?>">
            </div>
            <div class="flex-align">
                <label for="email">Email </label>
                <input type="email" name="email" class="email" placeholder="Email" value="<?php echo ( isset( $_POST['email'] ) ? htmlspecialchars($_POST['email']) : '' ); ?>">
            </div>
            <div class="flex-align">
                <label for="username">Username </label>
                <input type="text" name="username" class="username" placeholder="Username" value="<?php echo ( isset( $_POST['username'] ) ? htmlspecialchars($_POST['username']) : '' ); ?>">
            </div>
            <div class="flex-align">
                <label for="password">Password </label>
                <input type="password" name="password" class="password">
            </div>
            <div flex="flex-align">
                <label for="repeat-password">Repeat Password </label>
                <input type="password" name="repeat-password" class="repeat-password">
            </div>
            <input type="submit" value="submit" name="submit" class="submit">
        </form>


    </fieldset>
</body>
</html>