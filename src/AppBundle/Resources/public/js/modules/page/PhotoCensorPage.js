define(['CriticalService', 'jquery-areaselect'], function (CriticalService) {
    PhotoCensorPage = function () {
        this._initPhoto();

        this._CriticalService = CriticalService;
    };

    PhotoCensorPage.prototype._CriticalService = null;

    PhotoCensorPage.prototype._initPhoto = function () {
        $('#photo').selectAreas({
            width: 940
        });

        $('#save').click(function() {
            var areaData = $('#photo').selectAreas('relativeAreas');

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
