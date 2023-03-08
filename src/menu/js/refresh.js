$(document).ready(function(){
    setInterval(function(){
        $("#dump").load("src/menu/includes/check-timeout.inc.php");
    }, 60000);
});