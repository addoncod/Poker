<?php

    session_start();
    
    $tableID = $_POST['table'];
    $login = $_SESSION['user'];

    include "../../auth/classes/dbh.classes.php";
    include "../classes/table.classes.php";
    include "../classes/player.classes.php";
    $player = new Player($tableID,$login);

    $player->joinTheTable();