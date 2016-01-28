define(['leaflet', 'CityMarker', 'LocationMarker', 'leaflet-locate'], function() {
    Map = function (mapId, settings) {
        this._mapId = mapId;

        this.settings = $.extend(this._defaults, settings);

        this._init();
    };

    Map.prototype._defaults = {
        tileLayerUrl: 'https://api.tiles.mapbox.com/v4/maltehuebner.i1c90m12/{z}/{x}/{y}.png',
        mapBoxAccessToken: 'pk.eyJ1IjoibWFsdGVodWVibmVyIiwiYSI6IjB5c2QtNXcifQ.I7OHZr0wtAvqE0wIY_psfg',
        mapAttribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
        stylesheetAddress: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.css',
        detectRetina: true,
        defaultLatitude: 51.0851708,
        defaultLongitude: 5.9692092,
        defaultZoom: 5,
        showZoomControl: true,
        zoomControlPosition: 'bottomright'
    };

    Map.prototype._init = function () {
        this._loadStyles();
        this._initMap();
        this._addTileLayer();
    };

    Map.prototype._loadStyles = function() {
        var link = $('<link>', {
            rel: 'stylesheet',
            type: 'text/css',
            href: this.settings.stylesheetAddress
        });

        link.appendTo('head');
    };

    Map.prototype._initMap = function () {
        var defaultLatLng = L.latLng(
            this.settings.defaultLatitude,
            this.settings.defaultLongitude
        );

        this.map = L.map(this._mapId,
            {
                zoomControl: false
            }
        );

        if (this.settings.showZoomControl) {
            this.addZoomControl(this.settings.zoomControlPosition);
        }

        this.map.setView(defaultLatLng, this.settings.defaultZoom);
    };

    Map.prototype.addZoomControl = function (zoomControlPosition) {
        var zoomControl = new L.Control.Zoom(
            {
                position: zoomControlPosition
            }
        );

        zoomControl.addTo(this.map);
    };

    Map.prototype._addTileLayer = function () {
        L.tileLayer(this.settings.tileLayerUrl + '?access_token=' + this.settings.mapBoxAccessToken, {
            attribution: this.settings.mapAttribution,
            detectRetina: this.settings.detectRetina
        }).addTo(this.map);
    };

    Map.prototype.addLayer = function(layer) {
        this.map.addLayer(layer);
    };

    Map.prototype.setView = function (latLng, zoom) {
        this.map.setView(latLng, zoom);
    };

    Map.prototype.fitBounds = function(bounds, options) {
        this.map.fitBounds(bounds, options);
    };

    return Map;
});
