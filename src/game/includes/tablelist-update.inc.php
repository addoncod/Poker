<?php

    session_start();
    if(!isset($_SESSION['tableID'])){
        exit();
    }
    $tableID = $_SESSION['tableID'];

    include "../../auth/classes/dbh.classes.php";
    include "../classes/table.classes.php";
    $table = new Table($tableID);
    
    $table = $table->updateTablePlayersList();  