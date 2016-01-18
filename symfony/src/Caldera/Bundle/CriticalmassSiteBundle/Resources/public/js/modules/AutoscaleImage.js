define([], function() {
    AutoscaleImage = function(context, options) {
        var $img = $(context);
        var parentWidth = $img.parent().width();

        $img.css('width', parentWidth);

        $img.load(function() {
            var ratio = 0;
            var width = $img.width();
            var height = $img.height();

            if (width > parentWidth) {
                ratio = parentWidth / width;
                $(this).css("height", height * ratio);
                height = height * ratio;
                width = width * ratio;
            }
        });
    };

    return AutoscaleImage;
});