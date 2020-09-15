define(['Map', 'leaflet'], function (Map) {
    HeatmapPage = function (context, options) {
        this._options = options;
    };

    HeatmapPage.prototype._heatmapIdentifier = '';
    HeatmapPage.prototype._map = '';

    HeatmapPage.prototype.setHeatmapIdentifier = function (heatmapIdentifier) {
        this._heatmapIdentifier = heatmapIdentifier;
    };

    HeatmapPage.prototype.setMapCenter = function (latitude, longitude) {
        this._mapCenter = L.latLng(latitude, longitude);
    };

    HeatmapPage.prototype.init = function () {
        this._map = new Map('heatmap');
        this._map.setMaxZoom(16);
        this._map.setMinZoom(10);

        L.tileLayer('/heatmaps/' + this._heatmapIdentifier + '/{z}/{x}/{y}.png').addTo(this._map);

        this._map.setView([this._mapCenter.lat, this._mapCenter.lng], 13);
    };

    return HeatmapPage;
});
