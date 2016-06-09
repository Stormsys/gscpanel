define(function() {
    function AdminViewController() {

    }

    AdminViewController.prototype = {

        //ViewController Interface Implementation
        ViewLoad : function ()
        {
            this.ViewResume();
        },
        ViewResume : function ()
        {
            $(document).on('change', 'select[name="ds_id"]', function(e) {
                var id = $(this).val();
                if(id != '')
                {
                    $.ajax({
                        url: '/api/admin/get-dedicatedserver-ip/' + id,
                        success: function(response)
                        {
                            $('input[name="ip"]').val(response.data);
                        }
                    });
                }
            });

            $(document).on('change', 'select[name="template_id"]', function(e) {
                var id = $(this).val();
                if(id != '')
                {
                    $.ajax({
                        url: '/api/admin/get-template-data/' + id,
                        success: function(response)
                        {
                            console.log(response);
                            $('input[name="slot_count"]').val(response.data.default_slots);
                            $('input[name="port"]').val(response.data.default_port);
                            $('input[name="paramater_overide"]').val(response.data.default_cmd);
                            $('input[name="slots"]').val(response.data.default_slots);
                        }
                    });
                }
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

    return new AdminViewController();
});