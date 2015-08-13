EditCityPage = function(settings) {
    this.settings = $.extend(this.$$defaults, settings);

    this.$$init();
};

EditCityPage.prototype.$$defaults = {
    mapContainerId: 'map',
    cityLatitudeInputSelector: '#city_latitude',
    cityLongitudeInputSelector: '#city_longitude',
    cityStandardLatitudeInputSelector: '#city_standardLatitude',
    cityStandardLongitudeInputSelector: '#city_standardLongitude',
    cityIsStandardableLocationInputSelector: '#city_isStandardableLocation',
    cityIsLocationInputSelector: '#city_standardLocation',
    cityMarkerPopupText: 'Zieh mich auf den Mittelpunkt der Stadt!',
    cityStandardLocationPopupText: 'Zieh mich auf den Treffpunkt!',
    defaultCenterLatitude: 51.163375,
    defaultCenterLongitude: 10.447683
};

EditCityPage.prototype.map = null;
EditCityPage.prototype.cityLatLng = null;
EditCityPage.prototype.standardLocationLatLng = null;
EditCityPage.prototype.defaultCenterLatLng = null;
EditCityPage.prototype.mapCenter = null;
EditCityPage.prototype.mapZoom = null;
EditCityPage.prototype.cityMarker = null;
EditCityPage.prototype.locationMarker = null;

EditCityPage.prototype.$$init = function() {
    this.$$initLatLngs();
    this.$$initMap();
    this.$$initCityMarker();
    this.$$initLocationMarker();
};

EditCityPage.prototype.$$initLatLngs = function() {
    var cityLatitude = $(this.settings.cityLatitudeInputSelector).val();
    var cityLongitude = $(this.settings.cityLongitudeInputSelector).val();

    if (this.isFloat(cityLatitude) && this.isFloat(cityLongitude)) {
        this.cityLatLng = L.latLng(cityLatitude, cityLongitude);

        alert(this.cityLatLng);
    }

    var standardLocationLatitude = $(this.settings.cityStandardLatitudeInputSelector).val();
    var standardLocationLongitude = $(this.settings.cityStandardLongitudeInputSelector).val();

    if (this.isFloat(standardLocationLatitude) && this.isFloat(standardLocationLongitude)) {
        this.standardLocationLatLng = L.latLng(standardLocationLatitude, standardLocationLongitude);
    }

    this.defaultCenterLatLng = L.latLng(this.settings.defaultCenterLatitude, this.settings.defaultCenterLongitude);
};

EditCityPage.prototype.$$initMap = function() {
    this.map = new Map(this.settings.mapContainerId, []);
    
    if (this.cityLatLng) {
        this.mapCenter = this.cityLatLng;
        this.mapZoom = 12;
    } else {
        this.mapCenter = this.defaultCenterLatLng;
        this.mapZoom = 6;
    }

    this.map.setView(this.mapCenter, this.mapZoom);
};

EditCityPage.prototype.$$initCityMarker = function() {
    this.cityMarker = new CityMarker(this.mapCenter, true);
    this.cityMarker.addTo(this.map);
    this.cityMarker.addPopupText(this.settings.cityMarkerPopupText, true);

    that = this;
    
    this.cityMarker.on('dragend', function (event) {
        var marker = event.target;
        var position = marker.getLatLng();

        that.$$updateCityPosition(position);
    });
};

EditCityPage.prototype.$$initLocationMarker = function() {
    var that = this;
    
    $(this.settings.cityIsStandardableLocationInputSelector).on('click', function () {
        if ($(this).prop('checked')) {
            that.$$addStandardLocationMarker();
            $(that.settings.cityIsLocationInputSelector).prop('disabled', '');
        }
        else {
            that.$$removeStandardLocationMarker();
            $(that.settings.cityIsLocationInputSelector).prop('disabled', 'disabled');
        }
    });
};

EditCityPage.prototype.$$addStandardLocationMarker = function() {
    var that = this;
    
    if (this.standardLocationLatLng) {
        this.locationMarker = new LocationMarker(this.standardLocationLatLng, true);
    } else {
        this.locationMarker = new LocationMarker(this.mapCenter, true);
    }

    this.locationMarker.addTo(this.map);
    this.locationMarker.addPopupText(this.settings.cityStandardLocationPopupText, true);

    this.locationMarker.on('dragend', function(event) {
        var marker = event.target;
        var position = marker.getLatLng();

        that.$$updateLocationPosition(position);
    });
    
};

EditCityPage.prototype.$$removeStandardLocationMarker = function() {
    this.locationMarker.removeFrom(this.map);
};

EditCityPage.prototype.isFloat = function(n) {
    n = parseFloat(n);
    return n === Number(n) && n % 1 !== 0;
};

EditCityPage.prototype.$$updateCityPosition = function(position) {
    $(this.settings.cityLatitudeInputSelector).val(position.lat);
    $(this.settings.cityLongitudeInputSelector).val(position.lng);
};

EditCityPage.prototype.$$updateLocationPosition = function(position) {
    $(this.settings.cityStandardLatitudeInputSelector).val(position.lat);
    $(this.settings.cityStandardLongitudeInputSelector).val(position.lng);
};