define(['BaseEntity', 'leaflet'], function() {
    MarkerEntity = function () {

    };

    MarkerEntity.prototype = new BaseEntity();
    MarkerEntity.prototype.constructor = MarkerEntity;

    MarkerEntity.prototype._latitude = null;
    MarkerEntity.prototype._longitude = null;
    MarkerEntity.prototype._marker = null;
    MarkerEntity.prototype._icon = null;

    MarkerEntity.prototype._defaultIconOptions = {
        iconSize: [25, 41],
        iconAnchor: [13, 41],
        popupAnchor: [0, -36],
        shadowSize: [41, 41],
        shadowAnchor: [13, 41],
        shadowUrl: '/bundles/calderacriticalmasssite/images/marker/defaultshadow.png',
        shadowRetinaUrl: '/bundles/calderacriticalmasssite/images/marker/defaultshadow.png'
    };

    MarkerEntity.prototype._initIcon = function() {
        var options = $.extend(this._defaultIconOptions, this._markerIconOptions);

        this._icon = L.icon(options);
    };

    MarkerEntity.prototype._createMarker = function() {
        if (!this._icon) {
            this._initIcon();
        }

        if (!this._marker) {
            this._marker = L.marker(
                [
                    this._latitude,
                    this._longitude
                ], {
                    icon: this._icon
                }
            );
        }
    };

    MarkerEntity.prototype.hasLocation = function () {
        return (this._latitude != null && this._longitude != null);
    };

    MarkerEntity.prototype.getLatitude = function() {
        return this._latitude;
    };

    MarkerEntity.prototype.getLongitude = function() {
        return this._longitude;
    };

    return MarkerEntity;
});