define(['Map', 'PhotoEntity'], function() {
    ViewPhotoPage = function() {
        this._installNavigation();
    };

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

    ViewPhotoPage.prototype.addPhoto = function(photoId, latitude, longitude, description, dateTime, filename) {
        this._photo = new PhotoEntity(photoId, latitude, longitude, description, dateTime, filename);
    };

    ViewPhotoPage.prototype.initMap = function() {
        this._map = new Map('map');

        this._map.setView(this._photo.getLatLng(), 14);

        this._photo.addToMap(this._map);
    };

    return ViewPhotoPage;
});