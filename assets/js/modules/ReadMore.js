define(['readmorejs'], function () {
    ReadMore = function (context, additionalOptions) {
        this._loadStyles();

        var options = {
            moreLink: '',
            lessLink: ''
        };

        if (additionalOptions) {
            options = $.merge(additionalOptions, options);
        }

        $(context).readmore(options);

        $('.readmore').click(function() {
            $(context).readmore('toggle');
            $(context).readmore('destroy');
            $(context).removeClass('readmore');
        });
    };

    ReadMore.prototype._loadStyles = function() {
        var $link = $('<link>', {
            rel: 'stylesheet',
            type: 'text/css',
            href: '/css/readmore.css'
        });

        $link.appendTo('head');
    };

    return ReadMore;
});
