define(['DrawMap', 'leaflet-polyline', 'leaflet-extramarkers', 'Geocoding', 'IncidentMarkerIcon'], function () {
    CyclewaysIncidentEditPage = function () {
        this._geocoding = new Geocoding();
        this._incidentMarkerIcon = new IncidentMarkerIcon();

        this._initMap();
        this._initDrawControl();
        this._initEventListeners();
    };

    CyclewaysIncidentEditPage.prototype._map = null;
    CyclewaysIncidentEditPage.prototype._markerIcon = null;
    CyclewaysIncidentEditPage.prototype._drawnItems = null;
    CyclewaysIncidentEditPage.prototype._geocoding = null;
    CyclewaysIncidentEditPage.prototype._incidentMarkerIcon = null;

    CyclewaysIncidentEditPage.prototype._initMap = function () {
        this._map = new DrawMap('map');
    };

    CyclewaysIncidentEditPage.prototype._initEventListeners = function () {
        this._map.map.on('draw:created', this._onMapDrawCallback.bind(this));
        this._map.map.on('draw:editstop', this._onMapEditCallback.bind(this));
        $('#incident_dangerLevel').on('change', this._updateMarkerIcon.bind(this));
        $('#incident_incidentType').on('change', this._updateMarkerIcon.bind(this));
    };

    CyclewaysIncidentEditPage.prototype._initDrawControl = function () {
        this._drawnItems = new L.FeatureGroup();
        this._drawnItems.addTo(this._map.map);
        this._createIcon();

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
                    icon: this._markerIcon
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

    CyclewaysIncidentEditPage.prototype._onMapEditCallback = function(e) {
        var element = this._drawnItems.getLayers().pop();
        var latLng = element.getLatLng();

        $('#incident_latitude').val(latLng.lat);
        $('#incident_longitude').val(latLng.lng);

        this._geocoding.searchAddressForLatLng(latLng.lat, latLng.lng, this._updateAddress);
    };

    CyclewaysIncidentEditPage.prototype._updateAddress = function(address) {
        $('#incident_street').val(address.road);
        $('#incident_houseNumber').val(address.house_number);
        $('#incident_suburb').val(address.suburb);
        $('#incident_district').val(address.city_district);
        $('#incident_zipCode').val(address.postcode);
    };

    CyclewaysIncidentEditPage.prototype._updateMarkerIcon = function(element) {
        this._createIcon();

        var latitude = $('#incident_latitude').val();
        var longitude = $('#incident_longitude').val();

        if (latitude && longitude) {
            this._drawnItems.clearLayers();

            var marker = L.marker([latitude, longitude], {
                icon: this._markerIcon
            });

            marker.addTo(this._drawnItems);
        }
    };

    CyclewaysIncidentEditPage.prototype._createIcon = function () {
        this._markerIcon = this._incidentMarkerIcon.createMarkerIcon(this._getCurrentIncidentType(), this._getCurrentDangerLevel());
    };

    CyclewaysIncidentEditPage.prototype._getCurrentDangerLevel = function () {
        return $('#incident_dangerLevel').val();
    };

    CyclewaysIncidentEditPage.prototype._getCurrentIncidentType = function () {
        return $('#incident_incidentType').val();
    };

    CyclewaysIncidentEditPage.prototype.setView = function (centerLatLng, zoom) {
        this._map.setView(centerLatLng, zoom);
    };

    return CyclewaysIncidentEditPage;
});
