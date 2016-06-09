
$(function() {

    require.config({
        baseUrl: '/assets/js'
    });

    require.onError = function(e) {
        console.warn(e);
    }

    require(['mastercontroller']);


    //------------------------------------------
    // Phone Navigation Toggle
    //------------------------------------------
    $('#sidebar-collapse').click(function() {
        if($('ul#navigation').is(':visible'))
            $('ul#navigation').slideUp('fast');
        else
            $('ul#navigation').slideDown('fast');
    });



    //------------------------------------------
    // Test Notification
    //------------------------------------------

    $('#notification').hide();


    //-------------------------------------------
    // Drag and Drop handlers
    //-------------------------------------------
    $(document).on('dragstart', function(e) {
        if(!$('.drag-hook:hover').length){
            e.preventDefault();
        }else{

            //reference for the inline function
            var current_draggable = e.target;

            //change the width so that the draggable item is not too big.
            $(current_draggable).addClass('clone');

            //reset the style in the dom.
            setTimeout(function(){
                $(current_draggable).addClass('drag');
                $(current_draggable).removeClass('clone');
            }, 10);
        }
    });
    $(document).on('dragend', function(e) {

        //reference for the inline function
        var current_draggable = e.target;

        //accounting for fast drags..
        setTimeout(function(){
            $(current_draggable).removeClass('drag');
        }, 10);

    });
    $(document).on('drop', function(e) {

        //reference for the inline function
        var current_draggable = e.target;
        if($(current_draggable).hasClass('droppable'))
        {
            $(current_draggable).removeClass('drag-over');
        }
    });
    $(document).on('dragenter', function(e) {

        //reference for the inline function
        var current_draggable = e.target;
        if($(current_draggable).hasClass('droppable'))
        {
            $(current_draggable).addClass('drag-over');
        }
    });
    $(document).on('dragover', function(e) {

        //reference for the inline function
        var current_draggable = e.target;
        if($(current_draggable).hasClass('droppable'))
        {
            e.stopPropagation();
            e.preventDefault();
        }
    });
    $(document).on('dragleave', function(e) {

        //reference for the inline function
        var current_draggable = e.target;
        if($(current_draggable).hasClass('droppable'))
        {
            $(current_draggable).removeClass('drag-over');
        }
    });

});