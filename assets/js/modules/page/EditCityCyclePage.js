define(['Map', 'LocationMarker', 'CityMarker', 'bootstrap-datepicker'], function () {
    var EditCityCyclePage = function (context, settings) {
        this.settings = $.extend(this._defaults, settings);

        this._init();
    };


    EditCityCyclePage.prototype._defaults = {
        mapContainerId: 'map',
        latitudeInputSelector: '#city_cycle_latitude',
        longitudeInputSelector: '#city_cycle_longitude',
        cityIsStandardableLocationInputSelector: '#standard_city_isStandardableLocation',
        cityIsLocationInputSelector: '#standard_city_standardLocation',
        cityMarkerPopupText: 'Ich bin der Mittelpunkt der Stadt!',
        locationPopupText: 'Zieh mich auf den Treffpunkt!',
        defaultCenterLatitude: 51.163375,
        defaultCenterLongitude: 10.447683
    };

    EditCityCyclePage.prototype.map = null;
    EditCityCyclePage.prototype.cityLatLng = null;
    EditCityCyclePage.prototype.standardLocationLatLng = null;
    EditCityCyclePage.prototype.defaultCenterLatLng = null;
    EditCityCyclePage.prototype.mapCenter = null;
    EditCityCyclePage.prototype.mapZoom = null;
    EditCityCyclePage.prototype.cityMarker = null;
    EditCityCyclePage.prototype.locationMarker = null;

    EditCityCyclePage.prototype._init = function () {
        this._initLatLngs();
        this._initMap();
        this._initDatePicker();
    };

    EditCityCyclePage.prototype._initLatLngs = function () {
        var cityLatitude = this.settings.cityCoords.latitude;
        var cityLongitude = this.settings.cityCoords.longitude;
        this.cityLatLng = L.latLng(cityLatitude, cityLongitude);

        var locationLatitude = $(this.settings.latitudeInputSelector).val();
        var locationLongitude = $(this.settings.longitudeInputSelector).val();

        if (this.isFloat(locationLatitude) && this.isFloat(locationLongitude)) {
            this.locationLatLng = L.latLng(locationLatitude, locationLongitude);
        }

        this.defaultCenterLatLng = L.latLng(this.settings.defaultCenterLatitude, this.settings.defaultCenterLongitude);
    };

    EditCityCyclePage.prototype._initMap = function () {
        var that = this;

        this.map = new Map(this.settings.mapContainerId, []);

        this.mapCenter = this.cityLatLng;
        this.mapZoom = 12;

        this.map.setView(this.mapCenter, this.mapZoom);

        this._initCityMarker();
        this._initLocationMarker();
    };

    EditCityCyclePage.prototype._initCityMarker = function () {
        this.cityMarker = new CityMarker(this.mapCenter, false);
        this.cityMarker.addToMap(this.map);
        this.cityMarker.addPopupText(this.settings.cityMarkerPopupText, true);
    };

    EditCityCyclePage.prototype._initLocationMarker = function () {
        var that = this;

        that._addLocationMarker();
    };

    EditCityCyclePage.prototype._addLocationMarker = function () {
        var that = this;

        var cityMarkerLatLng = this.cityMarker.getLatLng();

        if (this.locationLatLng) {
            this.locationMarker = new LocationMarker(this.locationLatLng, true);
        } else {
            this.locationMarker = new LocationMarker(cityMarkerLatLng, true);
        }

        this.locationMarker.addToMap(this.map);
        this.locationMarker.addPopupText(this.settings.locationPopupText, true);

        this.locationMarker.on('dragend', function (event) {
            var marker = event.target;
            var position = marker.getLatLng();

            that._updateLocationPosition(position);
        });

    };

    EditCityCyclePage.prototype.isFloat = function (n) {
        n = parseFloat(n);
        return n === Number(n) && n % 1 !== 0;
    };

    EditCityCyclePage.prototype._updateLocationPosition = function (position) {
        $(this.settings.latitudeInputSelector).val(position.lat);
        $(this.settings.longitudeInputSelector).val(position.lng);
    };

    EditCityCyclePage.prototype._initDatePicker = function () {
        $('.datepicker').datepicker({
            format: 'dd.mm.yyyy',
            autoclose: true,
            todayHighlight: true,
            weekStart: 1,
            zIndexOffset: 1000
        });
    };

    return EditCityCyclePage;
});
