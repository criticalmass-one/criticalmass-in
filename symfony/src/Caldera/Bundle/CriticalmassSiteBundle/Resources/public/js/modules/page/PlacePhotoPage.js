define(['Map', 'TrackEntity', 'SnapablePhotoMarker'], function () {
    PlacePhotoPage = function (context, options) {
        this._options = options;

        this._initMap();
    };

    PlacePhotoPage.prototype._track = null;

    PlacePhotoPage.prototype.init = function () {
        this._initMarker();
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

    PlacePhotoPage.prototype._initMarker = function () {
        var coord = this._options.photoCoord;

        this._marker = new SnapablePhotoMarker([coord.latitude, coord.longitude]);

        this._marker.addToMap(this._map);
        this._marker.snapToTrack(this._track);
    };

    PlacePhotoPage.prototype.setTrack = function (polylineLatLngs, colorRed, colorGreen, colorBlue) {
        this._track = new TrackEntity();
        this._track.setPolyline(polylineLatLngs, colorRed, colorGreen, colorBlue);
        this._track.addToMap(this._map);

        this._map.fitBounds(this._track.getBounds());
    };

    return PlacePhotoPage;
});