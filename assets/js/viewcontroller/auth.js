define(function() {
    function AuthViewController() {

    }

    AuthViewController.prototype = {

        //ViewController Interface Implementation
        ViewLoad : function ()
        {
            this.ViewResume();
        },
        ViewResume : function ()
        {
            $(document).on('click', '#login-submit', function(e) {
                $('#login-box').attr('data-loading', true);
                $.ajax({
                    url: '/api/user/login',
                    method: 'post',
                    data: {
                        username: $('#username').val(),
                        password: $('#password').val()
                    },
                    success: function(data) {
                        if(!data.success) {
                            $('#login-box').attr('data-error', true);
                            GSCP.ErrorController.Display(data.error_message)
                        }else{
                            GSCP.Navigate('/dashboard');
                        }
                    },
                    complete: function() {
                        $('#login-box').attr('data-loading', false);
                    }
                });
                setTimeout(function(){$('#login-box').attr('data-loading', false);}, 2500);
                e.preventDefault();
            });
        },
        ViewPause : function ()
        {

        },
        ViewUnload : function ()
        {
            //Unimplemented
        }
    };

    return new AuthViewController();
});