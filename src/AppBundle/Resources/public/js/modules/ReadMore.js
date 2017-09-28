define(['readmorejs'], function () {

    ReadMore = function (context, additionalOptions) {
        var options = {
            moreLink: '<a href="#" class="readmore-more">Mehr anzeigen</a>',
            lessLink: '<a href="#" class="readmore-less">Weniger anzeigen</a>'
        };

        if (additionalOptions) {
            options = $.merge(additionalOptions, options);
        }

        $(context).readmore(options);
    };

    return ReadMore;
});