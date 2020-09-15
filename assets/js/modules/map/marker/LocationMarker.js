define(['Marker'], function () {
    LocationMarker = function (latLng, draggable) {
        this._latLng = latLng;
        this._draggable = draggable;
    };

    LocationMarker.prototype = new Marker();
    LocationMarker.prototype.constructor = LocationMarker;

    LocationMarker.prototype._initIcon = function () {
        this._icon = L.ExtraMarkers.icon({
            icon: 'fa-bicycle',
            markerColor: 'yellow',
            shape: 'circle',
            prefix: 'far'
        });
    };

    return LocationMarker;
});