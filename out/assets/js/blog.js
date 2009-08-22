
$(document).ready( function() {

    $("a.hider").click( function(event) {
        $(this).next().toggle("slow");
        return false;
    });

});
