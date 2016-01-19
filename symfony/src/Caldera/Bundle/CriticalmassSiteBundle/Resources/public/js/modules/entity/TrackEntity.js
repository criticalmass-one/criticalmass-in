define(['leaflet', 'BaseEntity'], function() {
    TrackEntity = function () {

    };

    TrackEntity.prototype = new BaseEntity();
    TrackEntity.prototype.constructor = new TrackEntity();

    TrackEntity.prototype._polyline = null;

    TrackEntity.prototype.setPolyline = function (jsonData, colorRed, colorGreen, colorBlue) {
        this._polyline = L.polyline(jsonData, {color: 'rgb(' + colorRed + ',' + colorGreen + ', ' + colorBlue + ')'});
    };

    TrackEntity.prototype.getBounds = function () {
        return this._polyline.getBounds();
    };

    TrackEntity.prototype.removeLayer = function (trackLayer) {
        trackLayer.removeLayer(this._polyline);
    };

    TrackEntity.prototype.setLatLngs = function (latLngs) {
        return this._polyline.setLatLngs(latLngs);
    };

    return TrackEntity;
});