define(['Polyline.encoded', 'leaflet.extra-markers', 'Container', 'jquery', 'Map'], function () {
    AutoMap = function (mapId, settings) {
        this._mapId = mapId;

        this.settings = $.extend(this._defaults, settings);

        this._init();
    };

    AutoMap.prototype._defaults = {
        tileLayerUrl: 'https://tiles.caldera.cc/mapnik/{z}/{x}/{y}.png',
        mapAttribution: '<a href="https://www.openstreetmap.org/">Karte hergestellt aus OpenStreetMap-Daten</a> | Lizenz: <a href="https://opendatacommons.org/licenses/odbl/">Open Database License (ODbL)</a>',
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

    AutoMap.prototype._mapViewSet = false;

    AutoMap.prototype._init = function () {
        this._initMap();
        this._addTileLayer();
        this._autoSettings();
    };

    AutoMap.prototype._autoSettings = function () {
        this._autoSetView();
        this._autoSetPolyline();
        this._autoSetMarker();
        this._autoSetPolylineMarker();
        this._setLockMap();
    };

    AutoMap.prototype._autoSetView = function () {
        var latitude = this._$mapContainer.data('map-center-latitude');
        var longitude = this._$mapContainer.data('map-center-longitude');
        var zoomLevel = this._$mapContainer.data('map-zoomlevel');

        if (latitude && longitude && zoomLevel) {
            this.map.setView([latitude, longitude], zoomLevel);
            this._mapViewSet = true;
        }
    };

    AutoMap.prototype._autoSetPolyline = function () {
        var polylineString = this._$mapContainer.data('polyline');
        var polylineColorString = this._$mapContainer.data('polyline-color');

        if (polylineString && polylineColorString) {
            var polyline = L.Polyline.fromEncoded(polylineString, {color: polylineColorString});

            polyline.addTo(this.map);

            console.log(polyline);

            if (!this._mapViewSet) {
                this.map.fitBounds(polyline.getBounds());
            }
        }
    };

    AutoMap.prototype._autoSetMarker = function () {
        var latitude = this._$mapContainer.data('map-marker-latitude');
        var longitude = this._$mapContainer.data('map-marker-longitude');

        var markerColor = this._$mapContainer.data('map-marker-color') || 'yellow';
        var markerShape = this._$mapContainer.data('map-marker-shape') || 'square';
        var markerIcon = this._$mapContainer.data('map-marker-icon') || 'fa-bicycle';
        var markerClickable = this._$mapContainer.data('map-marker-clickable') || false;

        if (latitude && longitude && markerColor && markerShape && markerIcon) {
            var extraMarkerIcon = L.ExtraMarkers.icon({
                icon: markerIcon,
                markerColor: markerColor,
                shape: markerShape,
                prefix: 'far'
            });

            var marker = L.marker([latitude, longitude], {icon: extraMarkerIcon, clickable: markerClickable});
            marker.addTo(this.map);
        }
    };

    AutoMap.prototype._autoSetPolylineMarker = function () {
        var polyline = this._$mapContainer.data('map-markers-polyline-list');

        var markerColor = this._$mapContainer.data('map-marker-color') || 'yellow';
        var markerShape = this._$mapContainer.data('map-marker-shape') || 'square';
        var markerIcon = this._$mapContainer.data('map-marker-icon') || 'fa-bicycle';
        var markerClickable = this._$mapContainer.data('map-marker-clickable') || false;

        if (polyline && markerColor && markerShape && markerIcon) {
            var markerLatLngs = L.PolylineUtil.decode(polyline);

            var markerList = [];

            for (var index in markerLatLngs) {
                var latLng = markerLatLngs[index];

                var extraMarkerIcon = L.ExtraMarkers.icon({
                    icon: 'fa-bicycle',
                    markerColor: 'yellow',
                    shape: 'square',
                    prefix: 'far'
                });

                var marker = L.marker(latLng, {icon: extraMarkerIcon, clickable: markerClickable});

                markerList.push(marker);
            }

            var markerGroup = new L.featureGroup(markerList);

            markerGroup.addTo(this.map);

            if (!this._mapViewSet && markerLatLngs.length > 1) {
                this.map.fitBounds(markerGroup.getBounds());
            } else if (!this._mapViewSet) {
                var latLng = markerLatLngs.pop();

                this.setView(latLng, 13);
            }
        }
    };

    AutoMap.prototype._setLockMap = function () {
        var lockMap = this._$mapContainer.data('lock-map');

        if (lockMap == true) {
            this.disableInteraction();
        }
    };

    return AutoMap;
});
