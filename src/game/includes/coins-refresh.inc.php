<?php

    session_start();
    if(!isset($_SESSION['tableID'])){
        exit();
    }
    
    $tableID = $_SESSION['tableID'];
    $login = $_SESSION['user'];

    include "../../auth/classes/dbh.classes.php";
    include "../classes/table.classes.php";
    include "../classes/player.classes.php";
    $player = new Player($tableID,$login);

    $_SESSION['coins'] = $player->getPlayerValue("coins");
    echo "<span id='amountOfCoins'><b class='c'>".$_SESSION['coins']."$</b></span>";