<?php
    session_start();
    if(!isset($_SESSION['tableID'])){
        exit();
    }
    
    $tableID = $_SESSION['tableID'];
    $login = $_SESSION['user'];
    $raise = $_POST['raise'];

    include "../../auth/classes/dbh.classes.php";
    include "../classes/table.classes.php";
    include "../classes/player.classes.php";
    include "../classes/combination.classes.php";
    include "../classes/round.classes.php";
    include "../classes/raise.classes.php";
    $raise = new Raise($tableID,$login,$raise);

    $raise->makeRaise();