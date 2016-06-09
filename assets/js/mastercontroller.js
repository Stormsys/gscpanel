define(function() {

    window.GSCP = {};
    window.GSCP.Settings = {
        Debug: false
    };
    window.GSCP.ViewControllers = {};

    require([
        "globalcontrollers/notifications", "globalcontrollers/debug", "globalcontrollers/router", "globalcontrollers/error"],
        function(
            notification, debug, router,error) {
            window.GSCP.NotificationController = notification;
            window.GSCP.DebugController = debug;
            window.GSCP.RouterController = router;
            window.GSCP.ErrorController = error;

            crossroads.addRoute('', function(){});

            //shorthand
            var ViewControllers = window.GSCP.ViewControllers;
            var currentPage = undefined;

            var pageTranslations = {
                "default": "dashboard"
            };


            hasher.setHash('');

            var req = new XMLHttpRequest();
            req.open('GET', document.location, false);
            req.send(null);

            if(req.getResponseHeader("IS_ADMIN"))
                $('.admin-area').fadeIn();
            else
                $('.admin-area').fadeOut();

            if(req.getResponseHeader("PAGE_STATE"))
            {
                $('ul#navigation li a').removeClass('active');
                $('ul#navigation li a.' + req.getResponseHeader("PAGE_STATE")).addClass('active');
            }

            if(!req.getResponseHeader("IS_LOGGED_IN"))
            {
                $('.logged-in').hide();
                $('.logged-out').show();
            }
            else
            {
                $('.logged-in').show();
                $('.logged-out').hide();
            }
            //------------------------------------------
            // Ajax page transitions
            //------------------------------------------
            var pushed = false;
            var last_xhr = undefined;

            window.GSCP.Navigate = function(url) {
                ajax_page_load(url);
            }
            var ajax_page_load = function(url, error_count)
            {
                if(!error_count) error_count = 0;

                $('#page').hide();
                $('#loading').fadeIn();

                if(last_xhr && last_xhr.readystate != 4)
                    last_xhr.abort();

                last_xhr = $.ajax({
                    url: url,
                    cache: false,
                    success: function(page)
                    {
                        $('#page').html(page);

                    },
                    complete: function(e, status)
                    {
                        if(e.getResponseHeader("REQUEST_URL"))
                            url = e.getResponseHeader("REQUEST_URL");

                        if(e.getResponseHeader("IS_ADMIN"))
                            $('.admin-area').fadeIn();
                        else
                            $('.admin-area').fadeOut();

                        if(!e.getResponseHeader("IS_LOGGED_IN"))
                        {
                            $('.logged-in').hide();
                            $('.logged-out').show();
                        }
                        else
                        {
                            $('.logged-in').show();
                            $('.logged-out').hide();
                        }

                        if(e.getResponseHeader("PAGE_STATE"))
                        {
                            $('ul#navigation li a').removeClass('active');
                            $('ul#navigation li a.' + e.getResponseHeader("PAGE_STATE")).addClass('active');
                        }

                        if(url != window.location.pathname)
                        {
                            if(!pushed) pushed = true;
                            window.history.pushState({}, "Title", url);
                        }

                        manage_state(url);
                        if (status != "abort")
                        {
                            $('#page').attr('data-loading', false);
                            $('#loading').hide();
                            $('#page').fadeIn();

                        }
                    }
                });
            }

            var mirror = function (page)
            {
                if(pageTranslations[page] != undefined)
                    return pageTranslations[page];
                return page;
            }

            var manage_state = function (url)
            {
                var page = mirror((url + '/').match(/\/?(.*?)\/.*/)[1]);

                if(currentPage != undefined && page != currentPage && ViewControllers[currentPage] != undefined && ViewControllers[currentPage]['ViewPause'] != undefined)
                {
                    ViewControllers[currentPage].ViewPause();
                    GSCP.DebugController.Log(currentPage + ' ViewController Paused.');
                }

                if(page != currentPage && ViewControllers[page] != undefined && ViewControllers[page]['ViewResume'] != undefined)
                {
                    ViewControllers[page].ViewResume();
                    GSCP.DebugController.Log(page + ' ViewController Resumed.');
                }
                else if(ViewControllers[page] == undefined)
                {
                    require(['viewcontroller/' + page], function(controller) {
                        ViewControllers[page] = controller;

                        controller.ViewLoad();
                        GSCP.DebugController.Log(page + ' ViewController Loaded.');
                    });
                }

                currentPage = page;
            }

            //proxy all links through the ajax transitions.
            $(document).on('click', 'a', function(e) {
                var url = $(this).attr('href');
                if(url.length && url[0] != "#" && url[0] == "/") {
                    ajax_page_load(url);
                    e.preventDefault();
                }
            });

            //also handle page back
            $(window).bind('popstate', function (e) {
                if(pushed && window.location.href[window.location.href.length - 1] != '#' && !(window.location.href.indexOf('#') >= 0))
                    ajax_page_load(window.location.pathname);
            });


            //do state
            manage_state(window.location.pathname);
    });
});