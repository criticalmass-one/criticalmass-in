define(['leaflet', 'BaseEntity', 'leaflet-polyline'], function() {
    TrackEntity = function (trackId, polylineString, colorRed, colorGreen, colorBlue) {
        this._trackId = trackId;

        this.setColors(colorRed, colorGreen, colorBlue);

        this._polyline = L.Polyline.fromEncoded(polylineString, { color: this._color });
    };

    TrackEntity.prototype = new BaseEntity();
    TrackEntity.prototype.constructor = TrackEntity;

    TrackEntity.prototype._trackId = null;
    TrackEntity.prototype._polyline = null;
    TrackEntity.prototype._color = null;

    TrackEntity.prototype.addToMap = function(map) {
        if (this._polyline) {
            this._polyline.addTo(map.map);
        }
    };

    TrackEntity.prototype.addToLayer = function(trackLayer) {
        if (this._polyline) {
            trackLayer.addLayer(this._polyline);
        }
    };

    TrackEntity.prototype.setColors = function(colorRed, colorGreen, colorBlue) {
        this._colorRed = colorRed;
        this._colorGreen = colorGreen;
        this._colorBlue = colorBlue;

        this._color = 'rgb(' + colorRed + ', ' + colorGreen + ', ' + colorBlue + ')';
    };

    TrackEntity.prototype.getColorRed = function() {
        return this._colorRed;
    };

    TrackEntity.prototype.getColorGreen = function() {
        return this._colorGreen;
    };

    TrackEntity.prototype.getColorBlue = function() {
        return this._colorBlue;
    };

    TrackEntity.prototype.getLayer = function() {
        return this._polyline;
    };

    TrackEntity.prototype.getBounds = function () {
        return this._polyline.getBounds();
    };

    TrackEntity.prototype.removeFromLayer = function (trackLayer) {
        trackLayer.removeLayer(this._polyline);
    };

    TrackEntity.prototype.setLatLngs = function (latLngs) {
        return this._polyline.setLatLngs(latLngs);
    };

    return TrackEntity;
});