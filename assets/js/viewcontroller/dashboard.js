define(function() {
    function DashboardViewController() {
        this.allStatusTicker = undefined;
        this.frequentStatusTicker = undefined;

        this.ALL_STATUS_TICK_INTERVAL = 10000;
        this.FREQUENT_STATUS_TICK_INTERVAL = 5000;

        this.lastTickData = undefined;

        this.Routes = {
           Start: undefined,
           Stop: undefined,
           Restart: undefined
        };

        this.frequentServers = {
           //gsid: status_literal
        };
    }

    DashboardViewController.prototype = {
        NavigateFileBrowser: function(server_id, dir)
        {
            $('#file-man-holder').html('<img class="loader" src="/assets/img/loading-small.gif" alt="loading" />');
            $.ajax({
                url: '/ftp/dir/' + server_id + '&path=' + dir,
                success: function(page) {
                    $('#file-man-holder').html(page);
                }
            });
        },
        EditFile: function(server_id, file)
        {
            $('#file-man-holder').html('<img class="loader" src="/assets/img/loading-small.gif" alt="loading" />');
            $.ajax({
                url: '/ftp/view/' + server_id + '&path=' + file,
                success: function(page) {
                    $('#file-man-holder').html(page);
                }
            });
        },
        SetupFTPListener: function()
        {
            $(document).on('click', '#ftp-save', function(e) {
                var path = $(this).attr('data-save-path');
                $('#ftp-save').hide();
                $.ajax({
                    url: '/api/ftp/edit/' + $('#server-info').attr('data-gs-id') + '&path=' + path,
                    method: 'POST',
                    data: {
                        data: $('#ftp-edit-box').val()
                    },
                    success: function(data) {
                        if(!data.success)
                        {
                            GSCP.ErrorController.Display(data.error_message);
                        }
                    },
                    complete: function() {
                        $('#ftp-save').fadeIn();
                        GSCP.ViewControllers.dashboard.EditFile($('#server-info').attr('data-gs-id'), path);
                    }
                });
                e.preventDefault();
            });
            $(document).on('click', '.ftp-link', function(e) {
                var path = $(this).attr('data-path');
                GSCP.ViewControllers.dashboard.NavigateFileBrowser($('#server-info').attr('data-gs-id'), path);
                e.preventDefault();
            });
            $(document).on('click', 'table.file-manager tbody tr', function(e) {
                var path = $(this).attr('data-path');
                var type = $(this).attr('data-type');

                switch(type)
                {
                    case 'dir':
                        GSCP.ViewControllers.dashboard.NavigateFileBrowser($('#server-info').attr('data-gs-id'), path);
                        break;
                    case 'file-editable':
                        GSCP.ViewControllers.dashboard.EditFile($('#server-info').attr('data-gs-id'), path);
                        break;
                }
            });
        },

        //ViewController Interface Implementation
        ViewLoad : function ()
        {
            this.StartStatusTicker();
            this.SetupRoutes();
            this.SetupListeners();
            this.SetupFTPListener();
        },
        ViewResume : function ()
        {
            this.StartStatusTicker();
            this.SetupRoutes();
        },
        ViewPause : function ()
        {
            this.StopStatusTicker();
            this.KillRoutes();
        },
        ViewUnload : function ()
        {
            //Unimplemented
        },
        SetupListeners: function()
        {
            $(document).on('keyup', '.h1', function(e) {
                $(this).attr('size', $(this).val().length + 1);
            });
            $(document).on('resize', 'window', function(e) {
                var h1 =  $('.h1');
                h1.attr('size', h1.val().length + 1);
            });
            $(document).on('change', '.account-form input', function(e) {
                $('#ma-done').hide();
                $('#ma-loading').hide();
                $('#account-submit').fadeIn();
            });
            $(document).on('submit', '#account-form', function(e) {
                e.preventDefault();
            });
            $(document).on('click', '#account-submit', function(e) {
                var cnt = true;
                $('#ma-done').hide();
                $('#account-submit').hide();
                $('#ma-loading').fadeIn();

                if($('#account-pw').val())
                {
                    cnt = false;
                    if($('#account-pw').val().length > 5 && $('#account-pw').val() == $('#account-pw-confirm').val())
                    {

                        cnt = true;
                    }
                    else
                    {
                        $('#ma-done').hide();
                        $('#ma-loading').hide();
                        $('#account-submit').fadeIn();
                        GSCP.ErrorController.Display('Invalid password Specified...');
                    }
                }
                if(cnt)
                {
                    $.ajax({
                       url: '/api/user/update',
                       method: 'POST',
                       data: {
                           firstname: $('#account-firstname').val(),
                           lastname:  $('#account-lastname').val(),
                           email: $('#account-email').val(),
                           password: $('#account-pw').val()
                       },
                       success: function (data) {
                            if(data.success)
                            {
                                $('#account-submit').hide();
                                $('#ma-loading').hide();
                                $('#ma-done').fadeIn();
                            }
                            else
                            {
                                $('#ma-done').hide();
                                $('#ma-loading').hide();
                                $('#account-submit').fadeIn();
                                GSCP.ErrorController.Display(data.error_message);
                            }
                       }
                    });
                }
            });


        },
        KillRoutes: function ()
        {
            this.Routes.Start.dispose();
            this.Routes.Stop.dispose();
            this.Routes.Restart.dispose();
        },
        SetupRoutes: function ()
        {
            this.Routes.Start = crossroads.addRoute('/server/{gsid}/start', function(gsid) {
                $.ajax({
                    url: '/api/gameservers/start/' + gsid,
                    success: function (data)
                    {
                        if(!data.success)
                        {
                            GSCP.ErrorController.Display(data.error_message);
                        }
                    },
                    complete: function ()
                    {
                        hasher.setHash('');
                        $('#server-info[data-gs-id="' + gsid + '"]').attr('data-status', 'pending');
                        $('ul#server-list li[data-gameserver-id="' + gsid + '"]').attr('data-status', 'pending');
                    }
                });
                $('#server-info[data-gs-id="' + gsid + '"]').attr('data-status', 'pending');
                $('ul#server-list li[data-gameserver-id="' + gsid + '"]').attr('data-status', 'pending');
            });


            this.Routes.Stop = crossroads.addRoute('/server/{gsid}/stop', function(gsid) {
                $.ajax({
                    url: '/api/gameservers/stop/' + gsid,
                    success: function (data)
                    {
                        if(!data.success)
                        {
                            GSCP.ErrorController.Display(data.error_message);
                        }
                    },
                    complete: function ()
                    {
                        hasher.setHash('');
                        $('#server-info[data-gs-id="' + gsid + '"]').attr('data-status', 'pending');
                        $('ul#server-list li[data-gameserver-id="' + gsid + '"]').attr('data-status', 'pending');
                    }
                });
                $('#server-info[data-gs-id="' + gsid + '"]').attr('data-status', 'pending');
                $('ul#server-list li[data-gameserver-id="' + gsid + '"]').attr('data-status', 'pending');
            });


            this.Routes.Restart = crossroads.addRoute('/server/{gsid}/restart', function(gsid) {
                $.ajax({
                    url: '/api/gameservers/restart/' + gsid,
                    success: function (data)
                    {
                        if(!data.success)
                        {
                            GSCP.ErrorController.Display(data.error_message);
                        }
                    },
                    complete: function ()
                    {
                        hasher.setHash('');
                        $('#server-info[data-gs-id="' + gsid + '"]').attr('data-status', 'pending');
                        $('ul#server-list li[data-gameserver-id="' + gsid + '"]').attr('data-status', 'pending');
                    }
                });
                $('#server-info[data-gs-id="' + gsid + '"]').attr('data-status', 'pending');
                $('ul#server-list li[data-gameserver-id="' + gsid + '"]').attr('data-status', 'pending');
            });

            this.Routes.Restart = crossroads.addRoute('/server/{gsid}/reinstall', function(gsid) {
                var conf= confirm("Are you sure you wish to Reinstall this server?\nall files and data will be lost!");
                if (conf== true){
                    $.ajax({
                        url: '/api/gameservers/reinstall/' + gsid,
                        success: function (data)
                        {
                            if(!data.success)
                            {
                                GSCP.ErrorController.Display(data.error_message);
                            }
                        },
                        complete: function ()
                        {
                            hasher.setHash('');
                            $('ul#server-list li[data-gameserver-id="' + gsid + '"]').attr('data-status', 'pending');
                            $('#server-info[data-gs-id="' + gsid + '"]').attr('data-status', 'pending');

                        }
                    });
                    $('#server-info[data-gs-id="' + gsid + '"]').attr('data-status', 'pending');
                    $('ul#server-list li[data-gameserver-id="' + gsid + '"]').attr('data-status', 'pending');
                }
                else
                {
                    hasher.setHash('');
                }
            });
        },

        //Status Ticker Implementation
        StartStatusTicker : function ()
        {
            this.allStatusTicker = setInterval(this.StatusTick.bind(this), this.ALL_STATUS_TICK_INTERVAL);
            this.frequentStatusTicker = setInterval(this.FrequentStatusTick.bind(this), this.FREQUENT_STATUS_TICK_INTERVAL);
        },
        StopStatusTicker : function ()
        {
            clearInterval(this.allStatusTicker);
            clearInterval(this.frequentStatusTicker);
        },
        StatusTick : function ()
        {
            GSCP.DebugController.Log('Dashboard Status Tick');

            var dashboardVC = this;
            $.ajax({
                url: '/api/gameservers/status-all/1',
                method: 'GET',
                success: function (data)
                {
                    GSCP.DebugController.Log('Dashboard Status Tick Result:');
                    GSCP.DebugController.Log(data);

                    if($('ul#server-list')[0] != undefined) {

                        $.each(data.data, function(i, o) {
                            var server_box = $('ul#server-list li[data-gameserver-id="' + o.gs_id + '"]');

                            if(server_box.attr('data-status') != o.literal_status) {
                                GSCP.NotificationController.Notify(o.nickname, 'Server Status has changed to ' + o.literal_status + '.');
                                server_box.attr('data-status', o.literal_status);
                            }
                        });
                    } else if($('#server-info')[0] != undefined) {

                        $.each(data.data, function(i, o) {
                                var server_box = $('#server-info[data-gs-id="' + o.gs_id + '"]');
                                if(server_box[0])
                                {
                                    if(server_box.attr('data-status') != o.literal_status) {
                                        GSCP.NotificationController.Notify(o.nickname, 'Server Status has changed to ' + o.literal_status + '.');
                                        server_box.attr('data-status', o.literal_status);
                                    }
                                }else{
                                    if(dashboardVC.lastTickData != undefined && dashboardVC.lastTickData[i].literal_status != o.literal_status) {
                                        GSCP.NotificationController.Notify(o.nickname, 'Server Status has changed to ' + o.literal_status + '.');
                                    }
                                }
                        });
                    } else if(dashboardVC.lastTickData != undefined) {
                        $.each(data.data, function(i, o) {
                            if(dashboardVC.lastTickData[i].literal_status != o.literal_status) {
                                GSCP.NotificationController.Notify(o.nickname, 'Server Status has changed to ' + o.literal_status + '.');
                            }
                        });
                    }

                    dashboardVC.lastTickData = data.data;
                }
            });
        },
        FrequentStatusTick : function ()
        {
            //Used to update critical changes such as restart events.
        }
    };

    return new DashboardViewController();
});