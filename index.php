<?php
    session_start();

    if((isset($_SESSION['loggedin'])) && ($_SESSION['loggedin']==true))
    {
        header('Location: menu');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<html>

<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="src/auth/css/style.css">
    <script src="src/auth/js/zoom.js"></script>
</head>

<body>

<div class="banner">
    <h1 id="title">Poker</h1>
</div>

<div class="loginSystem">

    <div class="loginForms">

        <h1>Login</h1>
        <form action="src/auth/includes/login.inc" method="post">
            <input type="text" name="uid" placeholder="Username">
            <input type="password" name="pwd" placeholder="Password">
            <br>
            <button type="submit" name="submit">Log In</button>
        </form>

        <h1>Sign Up</h1>
        <form action="src/auth/includes/signup.inc" method="post">
            <input type="text" name="uid" placeholder="Username">
            <input type="password" name="pwd" placeholder="Password">
            <input type="password" name="pwdrepeat" placeholder="Repeat Password">
            <input type="text" name="email" placeholder="E-Mail">
            <br>
            <button type="submit" name="submit">Sign Up</button>
        </form>

    </div>

</div>

</body>

</html>