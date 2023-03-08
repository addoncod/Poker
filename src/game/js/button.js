$(document).ready(function(){
    $("#call").click(function(){
        $("#dump").load("src/game/includes/call.inc.php");
        $("#coins").load("src/game/includes/coins-refresh.inc.php");
    });
});

$(document).ready(function(){
    $("#check").click(function(){
        $("#dump").load("src/game/includes/check.inc.php");
    });
});

$(document).ready(function(){
    $("#fold").click(function(){
        $("#dump").load("src/game/includes/fold.inc.php");
    });
});

$(document).ready(function(){
    $("#allin").click(function(){
        $("#dump").load("src/game/includes/all-in.inc.php");
    });
});

$(document).ready(function(){
    $("#showresult").click(function(){
        $("#dump").load("src/game/includes/showresults.inc.php");
    });
});

$(document).ready(function(){
    $("#prsbtn").click(function(){
        $("#previousRoundSummary").load("src/game/includes/get-previousroundsummary.inc.php");
    });
});