define(['DrawMap', 'leaflet-polyline', 'leaflet-extramarkers'], function () {
    IncidentEditPage = function () {
        this._initMap();
        this._initMarkerIcon();
        this._initDrawControl();
        this._initDrawableStuff();
    };

    IncidentEditPage.prototype._map = null;
    IncidentEditPage.prototype._markerIcon = null;
    IncidentEditPage.prototype._drawnItems = null;

    IncidentEditPage.prototype._initMap = function () {
        this._map = new DrawMap('map');
    };

    IncidentEditPage.prototype._initMarkerIcon = function () {
        this._markerIcon = L.ExtraMarkers.icon({
            icon: 'fa-bomb',
            markerColor: 'red',
            shape: 'round',
            prefix: 'fa'
        });
    };

    IncidentEditPage.prototype._initDrawControl = function () {
        this._drawnItems = new L.FeatureGroup();

        var drawControl = new L.Control.Draw({
            edit: {
                featureGroup: this._drawnItems
            },
            draw: {
                rectangle: false,
                circle: false,
                marker: {
                    icon: this._markerIcon
                }
            }
        });

        this._map.map.addControl(drawControl);
    };

    IncidentEditPage.prototype._initDrawableStuff = function () {
        var that = this;

        this._map.map.on('draw:created', function (e) {
            var type = e.layerType,
                layer = e.layer;

            if (type == 'polyline') {
                var latLngList = layer.getLatLngs();

                var polyline = L.PolylineUtil.encode(latLngList);

                $('#incident_polyline').val(polyline);
                $('#incident_geometryType').val('polyline');
            }

            if (type == 'polygon') {
                var latLngList = layer.getLatLngs();

                var polyline = L.PolylineUtil.encode(latLngList);

                $('#incident_polyline').val(polyline);
                $('#incident_geometryType').val('polygon');
            }

            if (type == 'marker') {
                var latLng = layer.getLatLng();

                var polyline = L.PolylineUtil.encode([latLng]);

                //alert(polyline);
                $('#incident_polyline').val(polyline);
                $('#incident_geometryType').val('marker');
            }

            // Do whatever else you need to. (save to db, add to map etc)
            that._map.map.addLayer(layer);
        });
    };

    IncidentEditPage.prototype.setView = function (centerLatLng, zoom) {
        this._map.setView(centerLatLng, zoom);
    };


    return IncidentEditPage;
});
