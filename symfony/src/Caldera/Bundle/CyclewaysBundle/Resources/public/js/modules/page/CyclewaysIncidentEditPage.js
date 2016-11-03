define(['DrawMap', 'leaflet-polyline', 'leaflet-extramarkers', 'Geocoding'], function () {
    CyclewaysIncidentEditPage = function () {
        this._geocoding = new Geocoding();

        this._initMap();
        this._initMarkerIcon();
        this._initDrawControl();
        this._initDrawableStuff();
    };

    CyclewaysIncidentEditPage.prototype._map = null;
    CyclewaysIncidentEditPage.prototype._markerIcon = null;
    CyclewaysIncidentEditPage.prototype._drawnItems = null;
    CyclewaysIncidentEditPage.prototype._geocoding = null;

    CyclewaysIncidentEditPage.prototype._initMap = function () {
        this._map = new DrawMap('map');
    };

    CyclewaysIncidentEditPage.prototype._initMarkerIcon = function () {
        this._markerIcon = L.ExtraMarkers.icon({
            icon: 'fa-bomb',
            markerColor: 'red',
            shape: 'round',
            prefix: 'fa'
        });
    };

    CyclewaysIncidentEditPage.prototype._initDrawControl = function () {
        this._drawnItems = new L.FeatureGroup();
        this._drawnItems.addTo(this._map.map);

        var drawControl = new L.Control.Draw({
            edit: {
                featureGroup: this._drawnItems,
                remove: true
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

    CyclewaysIncidentEditPage.prototype._initDrawableStuff = function () {
        this._map.map.on('draw:created', this._onMapDrawCallback.bind(this));
        this._map.map.on('draw:editstop', this._onMapDrawCallback.bind(this));
    };

    CyclewaysIncidentEditPage.prototype._onMapDrawCallback = function(e) {
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

            $('#incident_latitude').val(latLng.lat);
            $('#incident_longitude').val(latLng.lng);

            $('#incident_geometryType').val('marker');

            this._geocoding.searchAddressForLatLng(latLng.lat, latLng.lng, this._updateAddress);
        }

        // Do whatever else you need to. (save to db, add to map etc)
        layer.addTo(this._drawnItems);
    };

    CyclewaysIncidentEditPage.prototype._updateAddress = function(address) {
        $('#incident_street').val(address.road);
        $('#incident_houseNumber').val(address.house_number);
        $('#incident_suburb').val(address.suburb);
        $('#incident_district').val(address.city_district);
        $('#incident_zipCode').val(address.postcode);

    };

    CyclewaysIncidentEditPage.prototype.setView = function (centerLatLng, zoom) {
        this._map.setView(centerLatLng, zoom);
    };


    return CyclewaysIncidentEditPage;
});
