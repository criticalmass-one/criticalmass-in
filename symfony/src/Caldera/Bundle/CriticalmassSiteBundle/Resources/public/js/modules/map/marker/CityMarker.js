define(['Marker'], function () {
    CityMarker = function (latLng, draggable) {
        this._latLng = latLng;
        this._draggable = draggable;
    };

    CityMarker.prototype = new Marker();
    CityMarker.prototype.constructor = CityMarker;

    CityMarker.prototype._initIcon = function () {
        this._icon = L.icon({
            iconUrl: this._baseIconUrl + 'marker-red.png',
            iconRetinaUrl: this._baseIconUrl + 'marker-red-2x.png',
            iconSize: [25, 41],
            iconAnchor: [13, 41],
            popupAnchor: [0, -36],
            shadowUrl: this._baseIconUrl + 'defaultshadow.png',
            shadowSize: [41, 41],
            shadowAnchor: [13, 41]
        });
    };

    return CityMarker;
});