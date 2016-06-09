define(function() {
    function DebugController() {

    }

    DebugController.prototype = {
        Log: function(message) {
            if(GSCP.Settings.Debug)
                console.log(message);
        }
    }

    return new DebugController();
});