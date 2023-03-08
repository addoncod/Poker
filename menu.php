<?php
    session_start();

    if (!isset($_SESSION['loggedin']))
    {
        header('Location: index');
        exit();
    }

    if(isset($_SESSION['onTable']) && $_SESSION['onTable']==true){
        header('Location: src/game/includes/disconnect.inc');
    }
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <title>Menu</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="src/menu/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" 
    integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" 
    crossorigin="anonymous"></script>
    <script src="src/menu/js/refresh.js"></script>
    <script src="src/menu/js/zoom.js"></script>
</head>

<body>

<?php
    echo "
    <div class='playerinfo'>
        <span id='welcome'>Welcome <b>".$_SESSION['user']."</b></span>
        <br>
        <p id='coins'><b>".$_SESSION['coins']."$</b></p>
        <br>
    </div>

    <div class='tables'>
        <form action='src/game/includes/join-the-table.inc.php' method='post'>
            <button type='submit' name='table' value='1'>Table 1</button>
            <button type='submit' name='table' value='2'>Table 2</button>
        </form>
    </div>

    <div class='logout'>
        <a href='src/auth/includes/logout.inc'>LOGOUT</a>
    </div>
    ";
?>

<div id="dump">
    
</div>
    
</body>

</html>