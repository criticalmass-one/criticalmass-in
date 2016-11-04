define(['Marker'], function () {
    IncidentMarker = function (latLng) {
        this._latLng = latLng;
    };

    IncidentMarker.prototype = new Marker();
    IncidentMarker.prototype.constructor = IncidentMarker;

    IncidentMarker.prototype._initIcon = function () {
        this._icon = L.ExtraMarkers.icon({
            icon: 'fa-camera',
            markerColor: 'yellow',
            shape: 'square',
            prefix: 'fa'
        });
    };

    return IncidentMarker;
});