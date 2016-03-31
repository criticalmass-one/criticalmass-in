define(['Map'], function() {
    AutoMap = function (mapId, settings) {
        this._mapId = mapId;

        this.settings = $.extend(this._defaults, settings);

        this._init();
    };

    // do not call Map constructor directly as this will execute the map
    AutoMap.prototype = Object.create(Map.prototype);
    AutoMap.prototype.constructor = AutoMap;

    AutoMap.prototype._init = function () {
        this._loadStyles();
        this._initMap();
        this._addTileLayer();
        this._autoSettings();
    };

    AutoMap.prototype._autoSettings = function() {
        this._$mapContainer = $('#' + this._mapId);

        this._autoSetView();
    };

    AutoMap.prototype._autoSetView = function() {
        var latitude = this._$mapContainer.data('map-center-latitude');
        var longitude = this._$mapContainer.data('map-center-longitude');
        var zoomLevel = this._$mapContainer.data('map-zoomlevel');

        this.map.setView([latitude, longitude], zoomLevel);
    };

    AutoMap.prototype._autoSetMarker = function() {
        var latitude = this._$mapContainer.data('map-marker-latitude');
        var longitude = this._$mapContainer.data('map-marker-longitude');
        var markerColor = this._$mapContainer.data('map-marker-color');
        var markerShape = this._$mapContainer.data('map-marker-shape');
        var markerIconContent = this._$mapContainer.data('map-marker-icon-content');


        this.map.setView([latitude, longitude], zoomLevel);
    };

    return AutoMap;
});
