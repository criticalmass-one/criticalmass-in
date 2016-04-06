define(['Map', 'leaflet-polyline'], function() {
    AutoMap = function (mapId, settings) {
        this._mapId = mapId;

        this.settings = $.extend(this._defaults, settings);

        this._init();
    };

    Map.prototype._defaults = {
        tileLayerUrl: 'https://api.tiles.mapbox.com/v4/maltehuebner.i1c90m12/{z}/{x}/{y}.png',
        mapBoxAccessToken: 'pk.eyJ1IjoibWFsdGVodWVibmVyIiwiYSI6IjB5c2QtNXcifQ.I7OHZr0wtAvqE0wIY_psfg',
        mapAttribution: '&copy; <a href="https://www.mapbox.com/about/maps/">Mapbox</a> &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        stylesheetAddress: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.css',
        detectRetina: true,
        defaultLatitude: 51.0851708,
        defaultLongitude: 5.9692092,
        defaultZoom: 5,
        showZoomControl: false,
        zoomControlPosition: 'bottomright'
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
        this._autoSetPolyline();
    };

    AutoMap.prototype._autoSetView = function() {
        var latitude = this._$mapContainer.data('map-center-latitude');
        var longitude = this._$mapContainer.data('map-center-longitude');
        var zoomLevel = this._$mapContainer.data('map-zoomlevel');

        if (latitude && longitude && zoomLevel) {
            this.map.setView([latitude, longitude], zoomLevel);
        }
    };

    AutoMap.prototype._autoSetPolyline = function() {
        var polylineString = this._$mapContainer.data('polyline');
        var polylineColorString = this._$mapContainer.data('polyline-color');

        if (polylineString && polylineColorString) {
            var polyline = L.Polyline.fromEncoded(polylineString, { color: polylineColorString });

            polyline.addTo(this.map);

            this.map.fitBounds(polyline);
        }
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
