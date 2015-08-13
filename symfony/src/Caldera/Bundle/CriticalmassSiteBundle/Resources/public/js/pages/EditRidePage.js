EditRidePage = function(settings) {
    this.settings = $.extend(this.$$defaults, settings);
};

EditRidePage.prototype.$$defaults = {
    mapContainerId: 'map',
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

EditRidePage.prototype.$$toggleLocationInput = function() {

    $(this.settings.rideLocationInputSelector).prop('disabled', !$(this.settings.rideHasLocationInputSelector).prop('checked'));
};

EditRidePage.prototype.$$toggleTimeInput = function() {
    var $hasTimeInput = $(this.settings.rideHasTimeInputSelector);

    $(this.settings.rideTimeMinuteInputSelector).prop('disabled', !$hasTimeInput.prop('checked'));
    $(this.settings.rideTimeHourInputSelector).prop('disabled', !$hasTimeInput.prop('checked'));
};

EditRidePage.prototype.init = function() {
    this.$$initLatLngs();
    this.$$initMap();
    this.$$initCityMarker();
    this.$$initLocationMarker();
    this.$$initEventListeners();
};

EditRidePage.prototype.$$initEventListeners = function() {
    var that = this;

    function toggleLocationFunction() {
        that.$$toggleLocationInput.call(that, this)
    }

    function toggleTimeFunction() {
        that.$$toggleTimeInput.call(that, this)
    }

    function checkFunction() {
        that.$$checkRideDate.call(that, this);
    }

    $(this.settings.rideHasLocationInputSelector).on('click', toggleLocationFunction);
    $(this.settings.rideHasTimeInputSelector).on('click', toggleTimeFunction);

    $(this.settings.rideDateDayInputSelector).on('click', checkFunction);
    $(this.settings.rideDateMonthInputSelector).on('click', checkFunction);
    $(this.settings.rideDateYearInputSelector).on('click', checkFunction);
};

EditRidePage.prototype.$$initLatLngs = function() {
    var rideLocationLatitude = $(this.settings.rideLatitudeInputSelector).val();
    var rideLocationLongitude = $(this.settings.rideLongitudeInputSelector).val();

    if (this.isFloat(rideLocationLatitude) && this.isFloat(rideLocationLongitude)) {
        this.rideLocationLatLng = L.latLng(rideLocationLatitude, rideLocationLongitude);
    }

    this.defaultCenterLatLng = L.latLng(this.settings.defaultCenterLatitude, this.settings.defaultCenterLongitude);
};

EditRidePage.prototype.$$initMap = function() {
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

EditRidePage.prototype.$$initCityMarker = function() {
    
    this.cityMarker = new CityMarker(this.mapCenter, false);
    this.cityMarker.addTo(this.map);
    this.cityMarker.addPopupText(this.settings.cityMarkerPopupText, true);
};

EditRidePage.prototype.$$initLocationMarker = function() {
    var that = this;

    if ($(this.settings.rideHasLocationInputSelector).prop('checked')) {
        this.$$addStandardLocationMarker();
    }
    
    $(this.settings.rideHasLocationInputSelector).on('click', function () {
        if ($(this).prop('checked')) {
            that.$$addStandardLocationMarker();
            //$(that.settings.cityIsLocationInputSelector).prop('disabled', '');
        }
        else {
            that.$$removeStandardLocationMarker();
            //$(that.settings.cityIsLocationInputSelector).prop('disabled', 'disabled');
        }
    });
};

EditRidePage.prototype.$$addStandardLocationMarker = function() {
    var that = this;
    
    if (this.rideLocationLatLng) {
        this.locationMarker = new LocationMarker(this.rideLocationLatLng, true);
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

EditRidePage.prototype.$$removeStandardLocationMarker = function() {
    this.locationMarker.removeFrom(this.map);
};

EditRidePage.prototype.isFloat = function(n) {
    n = parseFloat(n);
    return n === Number(n) && n % 1 !== 0;
};

EditRidePage.prototype.$$updateLocationPosition = function(position) {
    $(this.settings.rideLatitudeInputSelector).val(position.lat);
    $(this.settings.rideLongitudeInputSelector).val(position.lng);
};

EditRidePage.prototype.$$searchForMonth = function(year, month) {
    for (index in this.rideDateList)
    {
        if (this.rideDateList[index].getFullYear() == year && this.rideDateList[index].getMonth() + 1 == month  && !this.$$isSelfMonth(year, month))
        {
            return true;
        }
    }

    return false;
};

EditRidePage.prototype.$$searchForMonthDay = function(year, month, day) {
    for (index in this.rideDateList)
    {
        console.log(this.rideDateList[index], year, month, day);
        if (this.rideDateList[index].getFullYear() == year && this.rideDateList[index].getMonth() + 1 == month && this.rideDateList[index].getDate() == day && !this.$$isSelfDateTime(year, month, day))
        {
            return true;
        }
    }

    return false;
};

EditRidePage.prototype.$$isSelfDateTime = function(year, month, day)
{
    return (this.rideDateTime && this.rideDateTime.getFullYear() == year && this.rideDateTime.getMonth() + 1 == month && this.rideDateTime.getDate() == day);
};

EditRidePage.prototype.$$isSelfMonth = function(year,  month)
{
    return (this.rideDateTime && this.rideDateTime.getFullYear() == year && this.rideDateTime.getMonth() + 1 == month);
};

EditRidePage.prototype.$$checkRideDate = function() {
    var day = $(this.settings.rideDateDayInputSelector).val();
    var month = $(this.settings.rideDateMonthInputSelector).val();
    var year = $(this.settings.rideDateYearInputSelector).val();

    var $messageDoubleMonthRide = $(this.settings.messageDoubleMonthRideSelector);
    var $messageDoubleDayRide = $(this.settings.messageDoubleDayRideSelector);
    var $submitButton = $(this.settings.submitButtonSelector);

    if (this.$$searchForMonthDay(year, month, day)) {
        $messageDoubleMonthRide.hide();
        $messageDoubleDayRide.show();
        $submitButton.prop('disabled', 'disabled');
    } else if (this.$$searchForMonth(year, month)) {
        $messageDoubleMonthRide.show();
        $messageDoubleDayRide.hide();
        $submitButton.prop('disabled', '');
    } else {
        $messageDoubleMonthRide.hide();
        $messageDoubleDayRide.hide();
        $submitButton.prop('disabled', '');
    }
};