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
    include "../classes/timeout.classes.php";
    $timeout = new Timeout($tableID,$login);

    $timeout->increaseTimeout();