define(['Map', 'TrackEntity', 'SnapablePhotoMarker', 'PhotoEntity', 'Container'], function() {
    RelocatePhotoPage = function(context, options) {
        this._options = options;


        this._photoContainer = new Container();

        this._initMap();
    };

    RelocatePhotoPage.prototype._track = null;

    RelocatePhotoPage.prototype.init = function() {

        this._photoContainer.addToMap(this._map);
    };

    RelocatePhotoPage.prototype._initMap = function() {
        this._map = new Map('map');
    };

    RelocatePhotoPage.prototype.setTrack = function(polylineLatLngs, colorRed, colorGreen, colorBlue) {
        this._track = new TrackEntity();
        this._track.setPolyline(polylineLatLngs, colorRed, colorGreen, colorBlue);
        this._track.addToMap(this._map);

        this._map.fitBounds(this._track.getBounds());
    };

    RelocatePhotoPage.prototype.addPhoto = function(photoId, latitude, longitude, description, dateTime, filename) {
        var photo = new PhotoEntity(photoId, latitude, longitude, description, dateTime, filename);

        var entityId = this._photoContainer.countEntities() + 1;

        photo.addToContainer(this._photoContainer, entityId);

        var that = this;
    };

    RelocatePhotoPage.prototype.relocateByTime = function(offsetSeconds) {

    };

    return RelocatePhotoPage;
});