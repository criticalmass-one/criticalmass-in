define(['CriticalService', 'AutoMap', 'PhotoEntity'], function(CriticalService) {
    ViewPhotoPage = function() {
        this._installNavigation();

        this._CriticalService = CriticalService;
    };

    ViewPhotoPage.prototype._CriticalService = null;
    ViewPhotoPage.prototype._nextPhotoUrl = null;
    ViewPhotoPage.prototype._previousPhotoUrl = null;

    ViewPhotoPage.prototype.setNextPhotoUrl = function(nextPhotoUrl) {
        this._nextPhotoUrl = nextPhotoUrl;
    };

    ViewPhotoPage.prototype.setPreviousPhotoUrl = function(previousPhotoUrl) {
        this._previousPhotoUrl = previousPhotoUrl;
    };

    ViewPhotoPage.prototype._installNavigation = function() {
        var that = this;

        $('body').keydown(function(e) {
            if ((e.keyCode || e.which) == 37 && that._previousPhotoUrl) {
                $(location).attr('href', that._previousPhotoUrl);
            }

            if ((e.keyCode || e.which) == 39 && that._nextPhotoUrl) {
                $(location).attr('href', that._nextPhotoUrl);
            }
        });
    };

    ViewPhotoPage.prototype.addPhoto = function(photoJson, filename) {
        this._photo = this._CriticalService.factory.createPhoto(photoJson);
        this._photo.setFilename(filename);
    };

    ViewPhotoPage.prototype.initMap = function() {
        this._map = new AutoMap('map');
    };

    return ViewPhotoPage;
});