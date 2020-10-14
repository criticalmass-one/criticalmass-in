define(['CriticalService', 'PhotoEntity', 'jquery.selectareas'], function (CriticalService) {
    PhotoCensorPage = function () {
        this._CriticalService = CriticalService;
    };

    PhotoCensorPage.prototype._CriticalService = null;
    PhotoCensorPage.prototype._photo = null;

    PhotoCensorPage.prototype.init = function () {
        var that = this;
        var $photo = $('#photo');

        var photoWidth = $photo.width();

        $photo.selectAreas({
            width: photoWidth
        });

        $('#save').click(function () {
            $(this).prop('disabled', true);

            var areaData = $('#photo').selectAreas('relativeAreas');

            var url = Routing.generate('caldera_criticalmass_photo_censor_short', {photoId: that._photo.getId()}, true);

            $.post(url + '?width=' + photoWidth,
                JSON.stringify(areaData)
            ).done(function (data) {
                window.location = document.referrer;
            });
        });
    };

    PhotoCensorPage.prototype.setPhoto = function (photoJson, filename) {
        this._photo = this._CriticalService.factory.createPhoto(photoJson);
        this._photo.setFilename(filename);
    };

    return PhotoCensorPage;
});
