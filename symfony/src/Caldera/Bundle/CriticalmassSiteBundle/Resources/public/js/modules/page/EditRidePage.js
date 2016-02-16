define(['Map', 'LocationMarker', 'typeahead', 'bloodhound'], function() {

    var EditRidePage = function(context, options) {
        this.settings = $.extend(this._defaults, options);
    };

    EditRidePage.prototype._defaults = {
        mapContainerId: 'map',
        citySlug: 'hamburg',
        rideLatitudeInputSelector: '#ride_latitude',
        rideLongitudeInputSelector: '#ride_longitude',
        rideHasLocationInputSelector: '#ride_hasLocation',
        rideLocationInputSelector: '#ride_location',
        rideHasTimeInputSelector: '#ride_hasTime',
        rideTimeMinuteInputSelector: '#ride_time_minute',
        rideTimeHourInputSelector: '#ride_time_hour',
        rideDateDayInputSelector: '#ride_date_day',
        rideDateMonthInputSelector: '#ride_date_month',
        rideDateYearInputSelector: '#ride_date_year',
        messageDoubleMonthRideSelector: '#doubleMonthRide',
        messageDoubleDayRideSelector: '#doubleDayRide',
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
    EditRidePage.prototype.rideDateList = [];
    EditRidePage.prototype.rideDateTime = null;

    EditRidePage.prototype.addRideDate = function(rideDate) {
        this.rideDateList.push(rideDate);
    };

    EditRidePage.prototype.setRideDateTime = function(rideDateTime) {
        this.rideDateTime = rideDateTime;
    };

    EditRidePage.prototype.setCityLatLng = function(cityLatLng) {
        this.cityLatLng = cityLatLng;
    };

    EditRidePage.prototype._toggleLocationInput = function() {

        $(this.settings.rideLocationInputSelector).prop('disabled', !$(this.settings.rideHasLocationInputSelector).prop('checked'));
    };

    EditRidePage.prototype._toggleTimeInput = function() {
        var $hasTimeInput = $(this.settings.rideHasTimeInputSelector);

        $(this.settings.rideTimeMinuteInputSelector).prop('disabled', !$hasTimeInput.prop('checked'));
        $(this.settings.rideTimeHourInputSelector).prop('disabled', !$hasTimeInput.prop('checked'));
    };

    EditRidePage.prototype.init = function() {
        this._initLatLngs();
        this._initMap();
        this._initCityMarker();
        this._initLocationMarker();
        this._initEventListeners();
        this._initLocationSearch();
    };

    EditRidePage.prototype._initEventListeners = function() {
        var that = this;

        function toggleLocationFunction() {
            that._toggleLocationInput.call(that, this)
        }

        function toggleTimeFunction() {
            that._toggleTimeInput.call(that, this)
        }

        function checkFunction() {
            that._checkRideDate.call(that, this);
        }

        $(this.settings.rideHasLocationInputSelector).on('click', toggleLocationFunction);
        $(this.settings.rideHasTimeInputSelector).on('click', toggleTimeFunction);

        $(this.settings.rideDateDayInputSelector).on('click', checkFunction);
        $(this.settings.rideDateMonthInputSelector).on('click', checkFunction);
        $(this.settings.rideDateYearInputSelector).on('click', checkFunction);
    };

    EditRidePage.prototype._initLatLngs = function() {
        var rideLocationLatitude = $(this.settings.rideLatitudeInputSelector).val();
        var rideLocationLongitude = $(this.settings.rideLongitudeInputSelector).val();

        if (this.isFloat(rideLocationLatitude) && this.isFloat(rideLocationLongitude)) {
            this.rideLocationLatLng = L.latLng(rideLocationLatitude, rideLocationLongitude);
        }

        this.defaultCenterLatLng = L.latLng(this.settings.defaultCenterLatitude, this.settings.defaultCenterLongitude);
    };

    EditRidePage.prototype._initMap = function() {
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

    EditRidePage.prototype._initCityMarker = function() {

        this.cityMarker = new CityMarker(this.mapCenter, false);
        this.cityMarker.addToMap(this.map);
        this.cityMarker.addPopupText(this.settings.cityMarkerPopupText, true);
    };

    EditRidePage.prototype._initLocationMarker = function() {
        var that = this;

        if ($(this.settings.rideHasLocationInputSelector).prop('checked')) {
            this._addStandardLocationMarker();
        }

        $(this.settings.rideHasLocationInputSelector).on('click', function () {
            if ($(this).prop('checked')) {
                that._addStandardLocationMarker();
                //$(that.settings.cityIsLocationInputSelector).prop('disabled', '');
            }
            else {
                that._removeStandardLocationMarker();
                //$(that.settings.cityIsLocationInputSelector).prop('disabled', 'disabled');
            }
        });
    };

    EditRidePage.prototype._addStandardLocationMarker = function() {
        var that = this;

        if (this.rideLocationLatLng) {
            this.locationMarker = new LocationMarker(this.rideLocationLatLng, true);
        } else {
            this.locationMarker = new LocationMarker(this.mapCenter, true);
        }

        this.locationMarker.addToMap(this.map);
        this.locationMarker.addPopupText(this.settings.cityStandardLocationPopupText, true);

        this.locationMarker.on('dragend', function(event) {
            var marker = event.target;
            var position = marker.getLatLng();

            that._updateLocationPosition(position);
        });

    };

    EditRidePage.prototype._removeStandardLocationMarker = function() {
        this.locationMarker.removeFromMap(this.map);
    };

    EditRidePage.prototype.isFloat = function(n) {
        n = parseFloat(n);
        return n === Number(n) && n % 1 !== 0;
    };

    EditRidePage.prototype._moveLocationMarker = function(latLng) {
        this.locationMarker.setLatLng(latLng);
    };

    EditRidePage.prototype._updateLocationPosition = function(position) {
        $(this.settings.rideLatitudeInputSelector).val(position.lat);
        $(this.settings.rideLongitudeInputSelector).val(position.lng);
    };

    EditRidePage.prototype._searchForMonth = function(year, month) {
        for (index in this.rideDateList)
        {
            if (this.rideDateList[index].getFullYear() == year && this.rideDateList[index].getMonth() + 1 == month  && !this._isSelfMonth(year, month))
            {
                return true;
            }
        }

        return false;
    };

    EditRidePage.prototype._searchForMonthDay = function(year, month, day) {
        for (index in this.rideDateList)
        {
            console.log(this.rideDateList[index], year, month, day);
            if (this.rideDateList[index].getFullYear() == year && this.rideDateList[index].getMonth() + 1 == month && this.rideDateList[index].getDate() == day && !this._isSelfDateTime(year, month, day))
            {
                return true;
            }
        }

        return false;
    };

    EditRidePage.prototype._isSelfDateTime = function(year, month, day)
    {
        return (this.rideDateTime && this.rideDateTime.getFullYear() == year && this.rideDateTime.getMonth() + 1 == month && this.rideDateTime.getDate() == day);
    };

    EditRidePage.prototype._isSelfMonth = function(year,  month)
    {
        return (this.rideDateTime && this.rideDateTime.getFullYear() == year && this.rideDateTime.getMonth() + 1 == month);
    };

    EditRidePage.prototype._checkRideDate = function() {
        var day = $(this.settings.rideDateDayInputSelector).val();
        var month = $(this.settings.rideDateMonthInputSelector).val();
        var year = $(this.settings.rideDateYearInputSelector).val();

        var $messageDoubleMonthRide = $(this.settings.messageDoubleMonthRideSelector);
        var $messageDoubleDayRide = $(this.settings.messageDoubleDayRideSelector);
        var $submitButton = $(this.settings.submitButtonSelector);

        if (this._searchForMonthDay(year, month, day)) {
            $messageDoubleMonthRide.hide();
            $messageDoubleDayRide.show();
            $submitButton.prop('disabled', 'disabled');
        } else if (this._searchForMonth(year, month)) {
            $messageDoubleMonthRide.show();
            $messageDoubleDayRide.hide();
            $submitButton.prop('disabled', '');
        } else {
            $messageDoubleMonthRide.hide();
            $messageDoubleDayRide.hide();
            $submitButton.prop('disabled', '');
        }
    };

    EditRidePage.prototype._initLocationSearch = function() {
        var that = this;

        this._bloodhound = new Bloodhound({
            datumTokenizer: function(data) {
                return Bloodhound.tokenizers.whitespace(data.location);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch: {
                url: '/' + this.settings.citySlug + '/locations',
                cache: true,
                ttl: 3600
            }
        });

        this._bloodhound.initialize();

        $($('#ride_location')).typeahead(
            {
                hint: false,
                highlight: true,
                minLength: 1,
                classNames: {
                    dataset: 'tt-dataset tt-dataset-results container'
                }
            },
            {
                name: 'results',
                source: this._bloodhound.ttAdapter(),
                displayKey: 'location',
                templates: {
                    suggestion: function(data) {
                        var html = '';
                        html += '<div class="row padding-top-small padding-bottom-small">';
                        html += '<div class="col-md-12">';
                        html += '<i class="fa fa-map-marker"></i>&nbsp;' + data.location;
                        html += '</div>';
                        html += '</div>';

                        return html;
                    }
                }
            }
        );

        $($('#ride_location')).bind('typeahead:select', function(ev, suggestion) {
            var latLng = {
                lat: suggestion.latitude,
                lng: suggestion.longitude
            };

            that._moveLocationMarker(latLng);
            that._updateLocationPosition(latLng);
            that.map.setView(latLng, 15);
        });
    };

    return EditRidePage;
});