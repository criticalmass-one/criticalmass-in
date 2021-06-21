define(['Map', 'LocationMarker', 'typeahead.jquery', 'bloodhound', 'Geocoding'], function () {

    var EditRidePage = function (context, options) {
        this.settings = $.extend(this._defaults, options);
        this._geocoding = new Geocoding();
    };

    EditRidePage.prototype._defaults = {
        mapContainerId: 'map',
        citySlug: 'hamburg',
        rideLatitudeInputSelector: '#ride_latitude',
        rideLongitudeInputSelector: '#ride_longitude',
        rideLocationInputSelector: '#ride_location',
        rideDateSelector: '#ride_dateTime_date',
        messageDoubleMonthRideSelector: '#doubleMonthRide',
        messageDoubleDayRideSelector: '#doubleDayRide',
        missingLocationMessage: 'Du hast bislang leider keinen Treffpunkt auf der Karte markiert. Bitte ziehe den gelben Marker auf den Treffpunkt.',
        submitButtonSelector: '#rideSubmitButton',
        cityMarkerPopupText: 'Ich bin der Mittelpunkt der Stadt',
        cityStandardLocationPopupText: 'Zieh mich auf den Treffpunkt!',
        defaultCenterLatitude: 51.163375,
        defaultCenterLongitude: 10.447683
    };

    EditRidePage.prototype.map = null;
    EditRidePage.prototype.cityLatLng = null;
    EditRidePage.prototype.rideLocationLatLng = null;
    EditRidePage.prototype.defaultCenterLatLng = null;
    EditRidePage.prototype.mapCenter = null;
    EditRidePage.prototype.mapZoom = null;
    EditRidePage.prototype.cityMarker = null;
    EditRidePage.prototype.locationMarker = null;
    EditRidePage.prototype.rideDateTime = null;

    EditRidePage.prototype.setRideDateTime = function (rideDateTime) {
        this.rideDateTime = rideDateTime;
    };

    EditRidePage.prototype.setCityLatLng = function (cityLatLng) {
        this.cityLatLng = cityLatLng;
    };

    EditRidePage.prototype.init = function () {
        this._initLatLngs();
        this._initMap();
        this._initCityMarker();
        this._initLocationMarker();
        this._initEventListeners();
    };

    EditRidePage.prototype._initEventListeners = function () {
        var that = this;

        function checkFunction() {
            that._checkRideDate.call(that, this);
        }

        $(this.settings.rideDateSelector).on('change', checkFunction);

        function checkLocation(form) {
            form.preventDefault();

            var rideLocationLatitude = $(that.settings.rideLatitudeInputSelector).val();
            var rideLocationLongitude = $(that.settings.rideLongitudeInputSelector).val();

            if (!rideLocationLatitude || !rideLocationLongitude) {
                alert(that.settings.missingLocationMessage);
            } else {
                $form = $('form');
                $form.off('submit');
                $form.submit();
            }
        }

        $('form').on('submit', checkLocation);
    };

    EditRidePage.prototype._initLatLngs = function () {
        var rideLocationLatitude = $(this.settings.rideLatitudeInputSelector).val();
        var rideLocationLongitude = $(this.settings.rideLongitudeInputSelector).val();

        if (this.isFloat(rideLocationLatitude) && this.isFloat(rideLocationLongitude)) {
            this.rideLocationLatLng = L.latLng(rideLocationLatitude, rideLocationLongitude);
        }

        this.defaultCenterLatLng = L.latLng(this.settings.defaultCenterLatitude, this.settings.defaultCenterLongitude);
    };

    EditRidePage.prototype._initMap = function () {
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

    EditRidePage.prototype._initCityMarker = function () {

        this.cityMarker = new CityMarker(this.mapCenter, false);
        this.cityMarker.addToMap(this.map);
        this.cityMarker.addPopupText(this.settings.cityMarkerPopupText, true);
    };

    EditRidePage.prototype._initLocationMarker = function () {
        var that = this;

        this._addStandardLocationMarker();
    };

    EditRidePage.prototype._addStandardLocationMarker = function () {
        var that = this;

        if (this.rideLocationLatLng) {
            this.locationMarker = new LocationMarker(this.rideLocationLatLng, true);
        } else {
            this.locationMarker = new LocationMarker(this.mapCenter, true);
        }

        this.locationMarker.addToMap(this.map);
        this.locationMarker.addPopupText(this.settings.cityStandardLocationPopupText, true);

        this.locationMarker.on('dragend', function (event) {
            var marker = event.target;
            var position = marker.getLatLng();

            that._updateLocationPosition(position);
        });

    };

    EditRidePage.prototype._removeStandardLocationMarker = function () {
        this.locationMarker.removeFromMap(this.map);
    };

    EditRidePage.prototype.isFloat = function (n) {
        n = parseFloat(n);
        return n === Number(n) && n % 1 !== 0;
    };

    EditRidePage.prototype._moveLocationMarker = function (latLng) {
        this.locationMarker.setLatLng(latLng);
    };

    EditRidePage.prototype._updateLocationPosition = function (position) {
        $(this.settings.rideLatitudeInputSelector).val(position.lat);
        $(this.settings.rideLongitudeInputSelector).val(position.lng);
    };

    EditRidePage.prototype._searchForMonth = function (year, month, successCallback, errorCallback) {
        var rideDate = [year, month].join('-');
        var url = Routing.generate('caldera_criticalmass_rest_ride_show', { citySlug: this.settings.citySlug, rideIdentifier: rideDate});

        $.ajax(url, {
            success: successCallback,
            error: errorCallback
        });
    };

    EditRidePage.prototype._searchForMonthDay = function (year, month, day, successCallback, errorCallback) {
        var rideDate = [year, month, day].join('-');
        var url = Routing.generate('caldera_criticalmass_rest_ride_show', { citySlug: this.settings.citySlug, rideIdentifier: rideDate});

        $.ajax(url, {
            success: successCallback,
            error: errorCallback
        });
    };

    EditRidePage.prototype._isSelfDay = function (year, month, day) {
        return (this.rideDateTime && this.rideDateTime.getFullYear() == year && this.rideDateTime.getMonth() + 1 == month && this.rideDateTime.getDate() == day);
    };

    EditRidePage.prototype._isSelfMonth = function (year, month) {
        return (this.rideDateTime && this.rideDateTime.getFullYear() == year && this.rideDateTime.getMonth() + 1 == month);
    };

    EditRidePage.prototype._checkRideDate = function () {
        var date = $(this.settings.rideDateSelector).val().split('.');

        var $messageDoubleMonthRide = $(this.settings.messageDoubleMonthRideSelector);
        var $messageDoubleDayRide = $(this.settings.messageDoubleDayRideSelector);
        var $submitButton = $(this.settings.submitButtonSelector);

        var daySuccessCallback = function() {
            $messageDoubleMonthRide.hide();
            $messageDoubleDayRide.show();
            $submitButton.prop('disabled', 'disabled');
        };

        var monthSuccessCallback = function() {
            $messageDoubleMonthRide.show();
            $messageDoubleDayRide.hide();
            $submitButton.prop('disabled', '');

            this._searchForMonthDay(date[2], date[1], date[0], daySuccessCallback);
        }.bind(this);

        var monthFailCallback = function() {
            $messageDoubleMonthRide.hide();
            $messageDoubleDayRide.hide();
            $submitButton.prop('disabled', '');
        }.bind(this);

        if (this._isSelfDay(date[2], date[1], date[0]) || this._isSelfMonth(date[2], date[1])) {
            return;
        }

        this._searchForMonth(date[2], date[1], monthSuccessCallback, monthFailCallback);
    };

    return EditRidePage;
});
