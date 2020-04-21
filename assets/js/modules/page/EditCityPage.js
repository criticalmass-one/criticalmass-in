define(['Map', 'LocationMarker', 'CityMarker', 'Geocoding'], function () {
    var EditCityPage = function (settings) {
        this.settings = $.extend(this._defaults, settings);

        this._geocoding = new Geocoding();
        this._geocoding.setCountry(this._defaults.country);
        this._geocoding.setState(this._defaults.state);

        this._init();
    };

    EditCityPage.prototype._defaults = {
        mapContainerId: 'map',
        cityLatitudeInputSelector: '#city_latitude',
        cityLongitudeInputSelector: '#city_longitude',
        cityMarkerPopupText: 'Zieh mich auf den Mittelpunkt der Stadt!',
        defaultCenterLatitude: 51.163375,
        defaultCenterLongitude: 10.447683
    };

    EditCityPage.prototype.map = null;
    EditCityPage.prototype.cityLatLng = null;
    EditCityPage.prototype.defaultCenterLatLng = null;
    EditCityPage.prototype.mapCenter = null;
    EditCityPage.prototype.mapZoom = null;
    EditCityPage.prototype.cityMarker = null;
    EditCityPage.prototype._geocoding = null;

    EditCityPage.prototype._init = function () {
        this._initLatLngs();
        this._initMap();
        this._initGeolocationEvents();
    };

    EditCityPage.prototype._initLatLngs = function () {
        var cityLatitude = $(this.settings.cityLatitudeInputSelector).val();
        var cityLongitude = $(this.settings.cityLongitudeInputSelector).val();

        if (this.isFloat(cityLatitude) && this.isFloat(cityLongitude)) {
            this.cityLatLng = L.latLng(cityLatitude, cityLongitude);
        }

        this.defaultCenterLatLng = L.latLng(this.settings.defaultCenterLatitude, this.settings.defaultCenterLongitude);
    };

    EditCityPage.prototype._initMap = function () {
        var that = this;

        this.map = new Map(this.settings.mapContainerId, []);

        if (this.cityLatLng) {
            this.mapCenter = this.cityLatLng;
            this.mapZoom = 12;

            this.map.setView(this.mapCenter, this.mapZoom);

            this._initCityMarker();
        } else {
            this._geocoding.searchState(this.settings.state, function (data) {
                if (data) {
                    that.mapCenter = L.latLng(data.lat, data.lon);
                } else {
                    that.mapCenter = L.latLng(that.settings.defaultCenterLatitude, that.settings.defaultCenterLongitude);
                }

                that.mapZoom = 5;

                that.map.setView(that.mapCenter, that.mapZoom);

                that._initCityMarker();
            });
        }
    };

    EditCityPage.prototype._initCityMarker = function () {
        this.cityMarker = new CityMarker(this.mapCenter, true);
        this.cityMarker.addToMap(this.map);
        this.cityMarker.addPopupText(this.settings.cityMarkerPopupText, true);

        that = this;

        this.cityMarker.on('dragend', function (event) {
            var marker = event.target;
            var position = marker.getLatLng();

            that._updateCityPosition(position);
        });
    };

    EditCityPage.prototype.isFloat = function (n) {
        n = parseFloat(n);
        return n === Number(n) && n % 1 !== 0;
    };

    EditCityPage.prototype._updateCityPosition = function (position) {
        $(this.settings.cityLatitudeInputSelector).val(position.lat);
        $(this.settings.cityLongitudeInputSelector).val(position.lng);
    };

    EditCityPage.prototype._moveCityMarker = function (latLng) {
        this.cityMarker.setLatLng(latLng);
    };

    EditCityPage.prototype._moveLocationMarker = function (latLng) {
        this.locationMarker.setLatLng(latLng);
    };

    EditCityPage.prototype._initGeolocationEvents = function () {
        var that = this;

        this._$searchCityButton = $('#search-city-button');
        this._$countrySelect = $('#city_region');

        this._$searchCityButton.on('click', function () {
            var cityName = $('#city_city').val();

            that._geocoding.searchCity(cityName, function (data) {
                that._handleGeocodingCity(data);
            });
        });

        this._$countrySelect.on('change', function (value) {
            var stateName = that._$countrySelect.find('option:selected').text();

            that._geocoding.setState(stateName);
        });
    };

    EditCityPage.prototype._handleGeocodingCity = function (data) {
        if (data && data.lat && data.lon) {
            var latLng = {
                lat: data.lat,
                lng: data.lon
            };

            this._updateCityPosition(latLng);
            this._moveCityMarker(latLng);
            this.map.setView(latLng, 15);
        } else {
            alert('Die Stadt wurde nicht gefunden. Bitte schiebe den Marker mauell auf den Mittelpunkt der Stadt.');
        }
    };

    return EditCityPage;
});