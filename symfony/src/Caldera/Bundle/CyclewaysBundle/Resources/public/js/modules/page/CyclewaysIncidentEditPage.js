define(['DrawMap', 'leaflet-polyline', 'leaflet-extramarkers', 'Geocoding'], function () {
    CyclewaysIncidentEditPage = function () {
        this._geocoding = new Geocoding();

        this._initMap();
        this._initDrawControl();
        this._initEventListeners();
    };

    CyclewaysIncidentEditPage.prototype._map = null;
    CyclewaysIncidentEditPage.prototype._markerIcon = null;
    CyclewaysIncidentEditPage.prototype._drawnItems = null;
    CyclewaysIncidentEditPage.prototype._geocoding = null;

    CyclewaysIncidentEditPage.prototype._initMap = function () {
        this._map = new DrawMap('map');
    };

    CyclewaysIncidentEditPage.prototype._initEventListeners = function () {
        this._map.map.on('draw:created', this._onMapDrawCallback.bind(this));
        this._map.map.on('draw:editstop', this._onMapDrawCallback.bind(this));
        $('#incident_dangerLevel').on('change', this._updateMarkerIcon.bind(this));
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
                polyline: false,
                polygon: false,
                marker: {
                    icon: this._createIcon()
                }
            }
        });

        this._map.map.addControl(drawControl);
    };

    CyclewaysIncidentEditPage.prototype._onMapDrawCallback = function(e) {
        var type = e.layerType,
            layer = e.layer;

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

    CyclewaysIncidentEditPage.prototype._updateMarkerIcon = function(element) {
        this._drawnItems.clearLayers();

        var latitude = $('#incident_latitude').val();
        var longitude = $('#incident_longitude').val();

        var marker = L.marker([latitude, longitude], {
            icon: this._createIcon()
        });

        marker.addTo(this._drawnItems);
    };

    CyclewaysIncidentEditPage.prototype._createIcon = function () {
        var markerColor = 'blue';
        var dangerLevel = this._getCurrentDangerLevel();

        switch (dangerLevel) {
            case 'low':
                markerColor = 'yellow';
                break;
            case 'normal':
                markerColor = 'orange';
                break;
            case 'high':
                markerColor = 'red';
                break;
            default:
                markerColor = 'blue';
                break;
        }

        return L.ExtraMarkers.icon({
            icon: 'fa-bomb',
            markerColor: markerColor,
            shape: 'square',
            prefix: 'fa'
        });
    };

    CyclewaysIncidentEditPage.prototype._getCurrentDangerLevel = function () {
        return $('#incident_dangerLevel').val();
    };

    CyclewaysIncidentEditPage.prototype.setView = function (centerLatLng, zoom) {
        this._map.setView(centerLatLng, zoom);
    };

    return CyclewaysIncidentEditPage;
});
