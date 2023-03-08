<?php

session_start();

if (!isset($_SESSION['loggedin']))
{
    header('Location: index');
    exit();
}

if(!isset($_SESSION['onTable']) || $_SESSION['onTable']==false)
{
    header('Location: menu');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Game</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="src/game/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" 
    integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" 
    crossorigin="anonymous"></script>
    <script src="src/game/js/button.js"></script>
    <script src="src/game/js/refresh.js"></script>
    <script src="src/game/js/submit.js"></script>
    <script src="src/game/js/zoom.js"></script>
    <script src="src/game/js/previous-round-summary-dropdown.js"></script>
</head>

<body>

<div class="summary">
    <button onclick="toggleVisbility()" id="prsbtn" class="dropbtn">Previous Game Info</button>
    <div id="previousRoundSummary" class="dropdown-content">
    </div>
</div>

<div class="table">
    <div id="tableVis">
        <div id="tableCardsHolder"><table id="tableCards"></table></div>
        <div id="pot"></div>
        <div id="info"></div>
        <div id='playerList'></div>
    </div>
</div>

<div class="hand">
    <table id='playerHand'><tr><th id='h1'>?</th><th id='h2'>?</th></tr></table>
</div>

<div class="buttonControllers">

    <button id='check'>CHECK</button>
    
    <button id='call'>CALL</button> 

    <button id='fold'>FOLD</button>

    <button id='allin'>ALL-IN</button>
    
    <form id='raiseForm' type='post'>
        <input type='number' name='raise' placeholder='0' id='raise'/> 
        <input type='button' id='submitraise' onclick='SubmitRaiseData();' value='RAISE'></button>
    </form>

</div>

<div class='playerInfo'>
    <div id='coinsRefresh'>
        <span id='amountOfCoins'><b class='c'><?php echo $_SESSION['coins']."$"?></b></span>
    </div>
</div>

<div class='return'>
    <a href='src/game/includes/disconnect.inc'>Return to menu</a>
</div>

<div id="dump">
    
</div>

</body>

</html>