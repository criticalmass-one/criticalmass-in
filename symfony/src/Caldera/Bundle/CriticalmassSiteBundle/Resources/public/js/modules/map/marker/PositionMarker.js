define(['Marker'], function() {
    PositionMarker = function (latLng, draggable) {
        this._latLng = latLng;
        this._draggable = draggable;

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

    PositionMarker.prototype = new Marker();
    PositionMarker.prototype.constructor = PositionMarker;

    PositionMarker.prototype.getColorString = function() {
        return 'rgb(255, 0, 0);';
    };

    PositionMarker.prototype._getHTML = function() {
        return '<div class="user-position-inline" style="border-color: ' + this.getColorString() + '"></div>';
    };

    PositionMarker.prototype._initIcon = function() {
        this._icon = L.divIcon({
            iconSize: new L.Point(50, 50),
            className: 'user-position',
            html: this._getHTML()
        });
    };

    PositionMarker.prototype.addToMap = function (map) {
        this._initIcon();

        this._marker = L.marker(this._latLng,
            {
                icon: this._icon,
                draggable: this._draggable
            });

        this._map = map;
        this._marker.addTo(this._map.map);
    };

    return PositionMarker;
});