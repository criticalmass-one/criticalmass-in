define(['Marker'], function() {
    SubrideMarker = function (latLng, draggable) {
        this._latLng = latLng;
        this._draggable = draggable;

        this._icon = L.icon({
            iconUrl: this._baseIconUrl + 'marker-green.png',
            iconRetinaUrl: this._baseIconUrl + 'marker-green-2x.png',
            iconSize: [25, 41],
            iconAnchor: [13, 41],
            popupAnchor: [0, -36],
            shadowUrl: this._baseIconUrl + 'defaultshadow.png',
            shadowSize: [41, 41],
            shadowAnchor: [13, 41]
        });
    };

    SubrideMarker.prototype = new Marker();
    SubrideMarker.prototype.constructor = SubrideMarker;

    return SubrideMarker;
});