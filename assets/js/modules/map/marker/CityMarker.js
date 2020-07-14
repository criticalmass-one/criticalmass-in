define(['Marker', 'leaflet.extra-markers'], function () {
    CityMarker = function (latLng, draggable) {
        this._latLng = latLng;
        this._draggable = draggable;
    };

    CityMarker.prototype = new Marker();
    CityMarker.prototype.constructor = CityMarker;

    CityMarker.prototype._initIcon = function () {
        this._icon = L.ExtraMarkers.icon({
            icon: 'fa-university',
            markerColor: 'red',
            iconColor: 'white',
            shape: 'circle',
            prefix: 'far'
        });
    };

    return CityMarker;
});
