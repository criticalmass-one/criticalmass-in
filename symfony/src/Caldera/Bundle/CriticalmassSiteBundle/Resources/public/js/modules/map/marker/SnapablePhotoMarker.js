define(['PhotoMarker', 'TrackEntity', 'leaflet-geometry', 'leaflet-snap'], function() {
    SnapablePhotoMarker = function (latLng) {
        this._latLng = latLng;
        this._draggable = true;
    };

    SnapablePhotoMarker.prototype = new PhotoMarker();
    SnapablePhotoMarker.prototype.constructor = SnapablePhotoMarker;

    SnapablePhotoMarker.prototype.snapToTrack = function(track) {
        this._track = track;

        this._marker.snapediting = new L.Handler.MarkerSnap(this._map.map, this._marker);
        this._marker.snapediting.addGuideLayer(this._track.getLayer());
        this._marker.snapediting.enable();
    };

    SnapablePhotoMarker.prototype._initIcon = function() {
        this._icon = L.icon({
            iconUrl: this._baseIconUrl + 'marker-yellow.png',
            iconRetinaUrl: this._baseIconUrl + 'marker-yellow-2x.png',
            iconSize: [25, 41],
            iconAnchor: [13, 41],
            popupAnchor: [0, -36],
            shadowUrl: this._baseIconUrl + 'defaultshadow.png',
            shadowSize: [41, 41],
            shadowAnchor: [13, 41]
        });
    };

    return SnapablePhotoMarker;
});