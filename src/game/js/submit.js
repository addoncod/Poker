function SubmitRaiseData() {
    var raise = $("#raise").val();

    $.post("src/game/includes/raise.inc.php", { raise: raise },
    function(data) {
        $('#dump').html(data);
        $('#raiseForm')[0].reset();
    });
}