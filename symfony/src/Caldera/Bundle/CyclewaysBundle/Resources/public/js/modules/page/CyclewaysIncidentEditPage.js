define(['CriticalService', 'DrawMap', 'leaflet-polyline', 'leaflet-extramarkers', 'leaflet-hash', 'Geocoding', 'IncidentMarkerIcon'], function (CriticalService) {
    CyclewaysIncidentEditPage = function () {
        this._geocoding = new Geocoding();
        this._incidentMarkerIcon = new IncidentMarkerIcon();
        this._CriticalService = CriticalService;

        this._initMap();
        this._initDrawControl();
        this._initEventListeners();
    };

    CyclewaysIncidentEditPage.prototype._CriticalService = null;
    CyclewaysIncidentEditPage.prototype._map = null;
    CyclewaysIncidentEditPage.prototype._markerIcon = null;
    CyclewaysIncidentEditPage.prototype._drawnItems = null;
    CyclewaysIncidentEditPage.prototype._geocoding = null;
    CyclewaysIncidentEditPage.prototype._incidentMarkerIcon = null;

    CyclewaysIncidentEditPage.prototype._initMap = function () {
        this._map = new DrawMap('map');
        this._CriticalService.setMap(this._map);
        this._hash = new L.Hash(this._map.map);
    };

    CyclewaysIncidentEditPage.prototype._initEventListeners = function () {
        this._map.map.on('draw:created', this._onMapDrawCallback.bind(this));
        this._map.map.on('draw:editstop', this._onMapEditCallback.bind(this));
        this._map.map.on('draw:deletestop', this._onMapDeleteCallback.bind(this));
        $('#incident_dangerLevel').on('change', this._updateMarkerIcon.bind(this));
        $('#incident_incidentType').on('change', this._updateMarkerIcon.bind(this));
        $('#incident_streetviewLink').on('change', this._resolveGoogleMapsLink.bind(this));
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
                polyline: {
                    repeatMode: false
                },
                polygon: false,
                marker: {
                    icon: this._markerIcon,
                    repeatMode: false
                }
            }
        });

        this._map.map.addControl(drawControl);
    };

    CyclewaysIncidentEditPage.prototype._onMapDrawCallback = function(e) {
        var type = e.layerType,
            layer = e.layer,
            latLng;

        if (type == 'polyline') {
            var latLngList = layer.getLatLngs();
            var polyline = L.PolylineUtil.encode(latLngList);
            latLng = latLngList[0];

            $('#incident_polyline').val(polyline);
            $('#incident_geometryType').val('polyline');
            $('#incident_latitude').val(latLng.lat);
            $('#incident_longitude').val(latLng.lng);

            this._createIcon();

            var marker = L.marker(latLng, {
                icon: this._markerIcon
            });

            marker.addTo(this._drawnItems);
        }
        
        if (type == 'marker') {
            latLng = layer.getLatLng();

            $('#incident_latitude').val(latLng.lat);
            $('#incident_longitude').val(latLng.lng);

            $('#incident_geometryType').val('marker');
        }

        this._geocoding.searchAddressForLatLng(latLng.lat, latLng.lng, this._updateAddress);

        layer.addTo(this._drawnItems);
    };

    CyclewaysIncidentEditPage.prototype._onMapEditCallback = function(e) {
        var element = this._drawnItems.getLayers().pop();
        var latLng = element.getLatLng();

        $('#incident_latitude').val(latLng.lat);
        $('#incident_longitude').val(latLng.lng);

        this._geocoding.searchAddressForLatLng(latLng.lat, latLng.lng, this._updateAddress);
    };

    CyclewaysIncidentEditPage.prototype._onMapDeleteCallback = function(e) {
        $('#incident_street').val('');
        $('#incident_houseNumber').val('');
        $('#incident_suburb').val('');
        $('#incident_district').val('');
        $('#incident_zipCode').val('');
        $('#incident_latitude').val('');
        $('#incident_longitude').val('');
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

        for (var index = 0; index < this._drawnItems.getLayers().length; ++index) {
            var layer = this._drawnItems.getLayers()[index];

            // evil: Only marker will return a latLng instead undefined
            if (layer.getLatLng()) {
                layer.setIcon(this._markerIcon);
                //layer.update();
                break;
            }
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

    CyclewaysIncidentEditPage.prototype.setFous = function (centerLatLng, zoom) {
        if (!window.location.hash) {
            this._map.setView(centerLatLng, zoom);
        }
    };

    CyclewaysIncidentEditPage.prototype._resolveGoogleMapsLink = function () {
        var url = Routing.generate('caldera_cycleways_incident_streetview_check');

        var googleUrl = $('#incident_streetviewLink').val();

        var data = {
            googleUrl: googleUrl
        };

        $.ajax({
            dataType: 'json',
            url: url,
            data: data,
            success: this._setMapMarkerFromGoogleMaps.bind(this)
        });

    };

    CyclewaysIncidentEditPage.prototype._setMapMarkerFromGoogleMaps = function (jsonResult) {
        if (!jsonResult) {
            return;
        }

        this._geocoding.searchAddressForLatLng(latitude, longitude, this._updateAddress.bind(this));

        if ($('#incident_latitude').val() || $('#incident_longitude').val()) {
            return;
        }

        var latitude = jsonResult.latitude;
        var longitude = jsonResult.longitude;
        var latLng = L.latLng(latitude, longitude);
        $('#incident_latitude').val(latLng.lat);
        $('#incident_longitude').val(latLng.lng);
        $('#incident_geometryType').val('marker');
        
        this._createIcon();

        this._drawnItems.clearLayers();

        var marker = L.marker(latLng, {
            icon: this._markerIcon
        });

        marker.addTo(this._drawnItems);

        this._map.setView(latLng, 15);
    };

    return CyclewaysIncidentEditPage;
});
