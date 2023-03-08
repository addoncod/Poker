$(document).ready(function(){
    setInterval(function(){
        $("#coinsRefresh").load("src/game/includes/coins-refresh.inc.php");
    }, 1000);
});

$(document).ready(function(){
    setInterval(function(){
        $("#pot").load("src/game/includes/get-pot.inc.php");
    }, 1000);
});

$(document).ready(function(){
    setInterval(function(){
        $("#playerList").load("src/game/includes/tablelist-update.inc.php");
    }, 1000);
});

$(document).ready(function(){
    setInterval(function(){
        $("#playerHand").load("src/game/includes/dealing-cards.inc.php");
    }, 4000);
});

$(document).ready(function(){
    setInterval(function(){
        $("#tableCards").load("src/game/includes/get-table.inc.php");
    }, 1000);
});

$(document).ready(function(){
    setInterval(function(){
        $("#info").load("src/game/includes/current-round.inc.php");
    }, 2000);
});

$(document).ready(function(){
    setInterval(function(){
        $("#dump").load("src/game/includes/dump-refresh.inc.php");
    }, 8000);
});

$(document).ready(function(){
    setInterval(function(){
        $("#dump").load("src/game/includes/increase-timeout.inc.php");
    }, 300000);
});

$(document).ready(function(){
    setInterval(function(){
        $("#dump").load("src/game/includes/check-timeout.inc.php");
    }, 60000);
});