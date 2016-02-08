define(['leaflet'], function() {
    Marker = function (latLng, draggable) {
        this._latLng = latLng;
        this._draggable = draggable;
    };

    Marker.prototype._latLng = null;
    Marker.prototype._draggable = false;
    Marker.prototype._map = false;
    Marker.prototype._icon = null;
    Marker.prototype._baseIconUrl = '/bundles/calderacriticalmasssite/images/marker/';

    Marker.prototype.getLatLng = function() {
        return this._marker.getLatLng();
    };

    Marker.prototype.setLatLng = function(latLng) {
        this._latLng = latLng;

        if (this._marker) {
            this._marker.setLatLng(latLng);
        }
    };

    Marker.prototype.addToMap = function (map) {
        this._marker = L.marker(this._latLng,
        {
            icon: this._icon,
            draggable: this._draggable
        });

        this._map = map;
        this._marker.addTo(this._map.map);
    };

    Marker.prototype.removeFrom = function (map) {
        map.map.removeLayer(this._marker);
    };

    Marker.prototype.addPopupText = function (popupText, openPopup) {
        if (openPopup) {
            this._marker.bindPopup(popupText).openPopup();
        } else {
            this._marker.bindPopup(popupText);
        }
    };

    Marker.prototype.on = function (event, func) {
        this._marker.on(event, func);
    };

    return Marker;
});