define(['jquery'], function ($) {
    Sharing = function (context, options) {
        this._initEventListeners(context);
    };

    Sharing.prototype._initEventListeners = function() {
        $('.open-share-window').click(this.openShareWindow);
    };

    Sharing.prototype.openShareWindow = function(e) {
        e.preventDefault();

        var $link = jQuery(this);
        var url = ($link.attr('href'));

        var popup = window.open(url, '_blank', 'toolbar=yes,scrollbars=yes,resizable=yes,width=500,height=400');

        var timer = setInterval(function () {
            if (popup.closed) {
                clearInterval(timer);
                refreshEstimatedParticipants($link);
            }
        }, 500);

        return false;
    };

    return Sharing;
});
