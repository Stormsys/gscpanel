define(function() {
    function ErrorController() {
        $('#error > .mbox > .eclose').click(function(){
            $('#error').fadeOut();
        });
    }

    ErrorController.prototype = {
        Display: function(message) {
            $('#error > .mbox > p').html(message);
            $('#error').fadeIn();
        }
    }

    return new ErrorController();
});