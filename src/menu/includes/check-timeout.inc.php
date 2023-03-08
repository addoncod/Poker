<?php
    session_start();

    include "../../auth/classes/dbh.classes.php";
    include "../../game/classes/table.classes.php";
    include "../../game/classes/player.classes.php";
    include "../../game/classes/timeout.classes.php";
    $timeout = new Timeout(null,null);

    $timeout->menuCheckTimeout();