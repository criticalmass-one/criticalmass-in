define(['leaflet', 'CityMarker', 'LocationMarker', 'leaflet-locate'], function () {
    Map = function (mapId, settings) {
        this._mapId = mapId;

        this.settings = $.extend(this._defaults, settings);

        this._init();
    };

    Map.prototype._defaults = {
        tileLayerUrl: 'https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}.png',
        mapAttribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        stylesheetAddress: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.css',
        detectRetina: true,
        defaultLatitude: 51.0851708,
        defaultLongitude: 5.9692092,
        defaultZoom: 5,
        showZoomControl: true,
        zoomControlPosition: 'bottomright'
    };

    Map.prototype._$mapContainer = null;

    Map.prototype._init = function () {
        this._loadStyles();
        this._initMap();
        this._addTileLayer();
    };

    Map.prototype._loadStyles = function () {
        var $link = $('<link>', {
            rel: 'stylesheet',
            type: 'text/css',
            href: this.settings.stylesheetAddress
        });

        $link.appendTo('head');
    };

    Map.prototype._initMap = function () {
        this._$mapContainer = $('#' + this._mapId);

        this.map = L.map(this._mapId,
            {
                zoomControl: false
            }
        );

        if (this.settings.showZoomControl) {
            this.addZoomControl(this.settings.zoomControlPosition);
        }
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

        L.tileLayer(this.settings.tileLayerUrl, {
            attribution: this.settings.mapAttribution,
            detectRetina: this.settings.detectRetina
        }).addTo(this.map);
    };

    Map.prototype.addLayer = function (layer) {
        this.map.addLayer(layer);
    };

    Map.prototype.setView = function (latLng, zoom) {
        this.map.setView(latLng, zoom);
    };

    Map.prototype.getCenter = function () {
        return this.map.getCenter();
    };

    Map.prototype.fitBounds = function (bounds, options) {
        this.map.fitBounds(bounds, options);
    };

    Map.prototype.getBounds = function () {
        return this.map.getBounds();
    };

    Map.prototype.on = function (event, callback) {
        this.map.on(event, callback);
    };

    Map.prototype.disableInteraction = function () {
        this._$mapContainer.css('cursor', 'default');
        this.map.dragging.disable();
        this.map.touchZoom.disable();
        this.map.doubleClickZoom.disable();
        this.map.scrollWheelZoom.disable();
        this.map.boxZoom.disable();
        this.map.keyboard.disable();

        if (this.map.tap) {
            this.map.tap.disable();
        }
    };

    Map.prototype.enableInteraction = function () {
        this._$mapContainer.css('cursor', 'move');
        this.map.dragging.enable();
        this.map.touchZoom.enable();
        this.map.doubleClickZoom.enable();
        this.map.scrollWheelZoom.enable();
        this.map.boxZoom.enable();
        this.map.keyboard.enable();

        if (this.map.tap) {
            this.map.tap.enable();
        }
    };

    return Map;
});
