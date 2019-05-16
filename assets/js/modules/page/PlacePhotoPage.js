define(['CriticalService', 'Map', 'RideEntity', 'TrackEntity', 'PhotoEntity', 'CityEntity', 'SnapablePhotoMarker'], function (CriticalService) {
    PlacePhotoPage = function (context, options) {
        this._CriticalService = CriticalService;

        this._options = options;

        this._initMap();
    };

    PlacePhotoPage.prototype._CriticalService = null;
    PlacePhotoPage.prototype._photo = null;
    PlacePhotoPage.prototype._track = null;
    PlacePhotoPage.prototype._ride = null;

    PlacePhotoPage.prototype.init = function () {
        var latLng = this._photo.getLatLng();

        if (!latLng) {
            latLng = this._ride.getLatLng();
        }

        this._initMarker(latLng);

        this._initMarkerEvent();
    };

    PlacePhotoPage.prototype._initMarkerEvent = function () {
        this._$latitudeInput = $(this._options.inputFields.latitude);
        this._$longitudeInput = $(this._options.inputFields.longitude);

        var that = this;

        this._marker.on('dragend', function () {
            var latLng = that._marker.getLatLng();

            that._$latitudeInput.val(latLng.lat);
            that._$longitudeInput.val(latLng.lng);
        });
    };

    PlacePhotoPage.prototype._initMap = function () {
        this._map = new Map('map');

        var coord = this._options.startCoord;

        this._map.setView([coord.latitude, coord.longitude], 13);
    };

    PlacePhotoPage.prototype._initMarker = function (latLng) {
        this._marker = new SnapablePhotoMarker(latLng);

        this._marker.addToMap(this._map);
        this._marker.snapToTrack(this._track);
    };

    PlacePhotoPage.prototype.addTrack = function (trackJson) {
        this._track = this._CriticalService.factory.createTrack(trackJson);

        this._track.addToMap(this._map);

        this._map.fitBounds(this._track.getBounds());
    };

    PlacePhotoPage.prototype.setPhoto = function (photoJson, filename) {
        this._photo = this._CriticalService.factory.createPhoto(photoJson);

        this._photo.setFilename(filename);
    };

    PlacePhotoPage.prototype.addRide = function (rideJson) {
        this._ride = this._CriticalService.factory.createRide(rideJson);
    };

    return PlacePhotoPage;
});
