define(['jquery', 'bootstrap-colorpicker'], function ($) {
    ProfileColorPage = function () {
        $colorPicker = $('#colorpicker');

        $colorPicker.colorpicker({
            format: 'rgb'
        });

        $colorPicker.on('changeColor', function(e) {
            var rgb = e.color.toRGB();

            $('#profile_color_colorRed').val(rgb.r);
            $('#profile_color_colorGreen').val(rgb.g);
            $('#profile_color_colorBlue').val(rgb.b);
        });
    };

    return ProfileColorPage;
});
