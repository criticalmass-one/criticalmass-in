define(['CriticalService', 'jquery-areaselect'], function (CriticalService) {
    PhotoCensorPage = function () {
        this._initPhoto();

        this._CriticalService = CriticalService;
    };

    PhotoCensorPage.prototype._CriticalService = null;

    PhotoCensorPage.prototype._initPhoto = function () {
        $('#photo').selectAreas({
            minSize: [30, 30],    // Minimum size of a selection
            maxSize: [400, 300],  // Maximum size of a selection
            onChanged: $.noop,    // fired when a selection is released
            onChanging: $.noop    // fired during the modification of a selection
        });

        $('#save').click(function() {
            var areaData = $('#photo').selectAreas('areas');

            console.log(areaData);

            $.post('http://criticalmass.cm/app_dev.php/hamburg/2016-10-28/photo/21281/censor',
                JSON.stringify(areaData)
            ).done(function(data) {
                console.log(data);
            });
        });
    };

    return PhotoCensorPage;
});
