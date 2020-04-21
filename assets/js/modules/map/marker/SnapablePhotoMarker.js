define(['PhotoMarker', 'TrackEntity', 'leaflet-geometry', 'leaflet-snap', 'leaflet.extra-markers'], function () {
    SnapablePhotoMarker = function (latLng) {
        this._latLng = latLng;
        this._draggable = true;
    };

    SnapablePhotoMarker.prototype = new PhotoMarker();
    SnapablePhotoMarker.prototype.constructor = SnapablePhotoMarker;

    SnapablePhotoMarker.prototype.snapToTrack = function (track) {
        this._track = track;

        this._marker.snapediting = new L.Handler.MarkerSnap(this._map.map, this._marker);
        this._marker.snapediting.addGuideLayer(this._track.getLayer());
        this._marker.snapediting.enable();
    };

    SnapablePhotoMarker.prototype._initIcon = function () {
        this._icon = L.ExtraMarkers.icon({
            icon: 'fa-camera',
            markerColor: 'yellow',
            iconColor: 'white',
            shape: 'square',
            prefix: 'far'
        });
    };

    return SnapablePhotoMarker;
});
