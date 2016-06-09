define(function() {
    $('#notification').hide();

    function NotificationData(title, message) {
        this.Title = title;
        this.Message = message;
    }

    function NotificationController() {
        this.DURATION = 2500;

        this.Queue = [];

        this.isQueueInProgress = false;

        $('#notification').hide();
    }

    NotificationController.prototype = {
        Notify: function(title, message) {
            this.Queue.push(new NotificationData(title, message));

            if(!this.isQueueInProgress)
                this._ProcessQueue();
        },
        _ProcessQueue: function() {
            var nextItem = this.Queue.shift();

            if(nextItem != undefined)
            {
                this.isQueueInProgress = true;
                this._ShowNotification(nextItem);
            }else
                this.isQueueInProgress = false;
        },
        _ShowNotification: function(notification) {
            $('#notification h1').html(notification.Title);
            $('#notification p').html(notification.Message);

            $('#notification').fadeIn('fast');
            setTimeout(this._PostNotify.bind(this), this.DURATION);
        },
        _PostNotify: function() {
            if($('#notification:hover')[0] != undefined) {
                setTimeout(this._PostNotify.bind(this), 500);
            } else {
                var NotificationController = this;
                $('#notification').fadeOut('fast', function() {
                    NotificationController._ProcessQueue();
                });
            }
        }
    }

    return new NotificationController();
});