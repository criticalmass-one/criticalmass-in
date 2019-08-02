define(['Map'], function (CriticalService) {
    HeatmapPage = function (context, options) {
        this._options = options;

        this._initMap();
    };

    PlacePhotoPage.prototype.init = function () {
    };

    PlacePhotoPage.prototype._initMap = function () {
        this._map = new Map('heatmap');

        this._map.setView([coord.latitude, coord.longitude], 13);
    };
    
    return PlacePhotoPage;
});
