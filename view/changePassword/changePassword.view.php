<?php include './model/changePassword.model.php' ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="changePassword.css">
</head>
<body>

    <?php if ($logged === 2) { ?>
        <h1 class="success">SUCCESS!</h1>
    <?php } else if ($logged === 1) { ?>
        <h1 class="error">WRONG PASSWORD</h1>
    <?php } ?>
    <fieldset>
        <legend>Change Password</legend>
        <form action="" method="post">
            <label for="old-password">Enter Old Password </label>
            <input type="password" name="old-password" class="old-password">
            <label for="new-password">Enter New Password</label>
            <input type="password" name="new-password" class="new-password">
            <input type="submit" value="submit" name="new-password-submit" class="new-password-submit">

            <input type="hidden" name="userID" value=<?=$id?>>
        </form>



    </fieldset>
</body>
</html>