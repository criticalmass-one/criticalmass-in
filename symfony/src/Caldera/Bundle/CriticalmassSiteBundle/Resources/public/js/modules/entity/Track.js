define(['leaflet'], function() {
    Track = function () {


    };

    Track.prototype._polyline = null;

    Track.prototype.setPolyline = function (jsonData, colorRed, colorGreen, colorBlue) {
        this._polyline = L.polyline(jsonData, {color: 'rgb(' + colorRed + ',' + colorGreen + ', ' + colorBlue + ')'});
    };

    Track.prototype.addTo = function (trackLayer) {
        alert(trackLayer);
        trackLayer.addLayer(this._polyline);
    };

    Track.prototype.getBounds = function () {
        return this._polyline.getBounds();
    };

    Track.prototype.removeLayer = function (trackLayer) {
        trackLayer.removeLayer(this._polyline);
    };

    Track.prototype.setLatLngs = function (latLngs) {
        return this._polyline.setLatLngs(latLngs);
    };

    return Track;
});