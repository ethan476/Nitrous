$(document).ready(function() {
    var anchor = window.location.hash.replace("#", "");
    //$(".collapse").collapse('hide');
    $("#" + anchor).collapse('show');
});