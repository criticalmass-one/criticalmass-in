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
            console.log($('#photo').selectAreas('areas'));
        });
    };

    return PhotoCensorPage;
});
