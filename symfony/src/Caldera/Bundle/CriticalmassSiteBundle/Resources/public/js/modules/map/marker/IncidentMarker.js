define(['Marker'], function () {
    IncidentMarker = function (latLng) {
        this._latLng = latLng;
    };

    IncidentMarker.prototype = new Marker();
    IncidentMarker.prototype.constructor = IncidentMarker;

    IncidentMarker.prototype._initIcon = function () {
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

    return IncidentMarker;
});