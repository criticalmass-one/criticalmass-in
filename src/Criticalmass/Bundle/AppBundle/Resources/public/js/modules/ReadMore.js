define(['readmorejs'], function () {

    ReadMore = function (context, additionalOptions) {
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

    return ReadMore;
});