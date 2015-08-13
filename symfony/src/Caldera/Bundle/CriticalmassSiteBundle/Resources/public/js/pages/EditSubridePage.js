EditSubridePage = function(settings) {
    this.settings = $.extend(this.$$defaults, settings);
};

EditSubridePage.prototype.$$defaults = {
    mapContainerId: 'map',
    subrideLatitudeInputSelector: '#subride_latitude',
    subrideLongitudeInputSelector: '#subride_longitude',
    cityMarkerPopupText: 'Ich bin der Mittelpunkt der Stadt',
    rideMarkerPopupText: 'Ich bin der Treffpunkt dieser Tour!',
    subrideMarkerPopupText: 'Ich bin der Treffpunkt dieser Mini-Mass!'
};

EditSubridePage.prototype.map = null;
EditSubridePage.prototype.cityLatLng = null;
EditSubridePage.prototype.rideLocationLatLng = null;
EditSubridePage.prototype.defaultCenterLatLng = null;
EditSubridePage.prototype.mapCenter = null;
EditSubridePage.prototype.mapZoom = null;
EditSubridePage.prototype.cityMarker = null;
EditSubridePage.prototype.locationMarker = null;
EditSubridePage.prototype.rideDateList = [];
EditSubridePage.prototype.rideDateTime = null;


EditSubridePage.prototype.setCityLatLng = function(cityLatLng) {
    this.cityLatLng = cityLatLng;
};

EditSubridePage.prototype.setRideLatLng = function(rideLatLng) {
    this.rideLatLng = rideLatLng;
};

EditSubridePage.prototype.init = function() {
    this.$$initLatLngs();
    this.$$initMap();
    this.$$initCityMarker();
    this.$$initLocationMarker();
    this.$$initSubrideMarker();
};

EditSubridePage.prototype.$$initEventListeners = function() {

};

EditSubridePage.prototype.$$initLatLngs = function() {
    var subrideLocationLatitude = $(this.settings.subrideLatitudeInputSelector).val();
    var subrideLocationLongitude = $(this.settings.subrideLongitudeInputSelector).val();

    if (this.isFloat(subrideLocationLatitude) && this.isFloat(subrideLocationLongitude)) {
        this.subrideLocationLatLng = L.latLng(subrideLocationLatitude, subrideLocationLongitude);
    } else if (this.rideLatLng) {
        this.subrideLocationLatLng = this.rideLatLng;
    } else {
        this.subrideLocationLatLng = this.cityLatLng;
        
    }
};

EditSubridePage.prototype.$$initMap = function() {
    this.map = new Map(this.settings.mapContainerId, []);

    this.map.setView(this.cityLatLng, 13);
};

EditSubridePage.prototype.$$initCityMarker = function() {
    this.cityMarker = new CityMarker(this.cityLatLng, false);
    this.cityMarker.addTo(this.map);
    this.cityMarker.addPopupText(this.settings.cityMarkerPopupText, true);
};

EditSubridePage.prototype.$$initLocationMarker = function() {
    if (this.rideLatLng) {
        this.cityMarker = new LocationMarker(this.rideLatLng, false);
        this.cityMarker.addTo(this.map);
        this.cityMarker.addPopupText(this.settings.rideMarkerPopupText, true);
    }
};

EditSubridePage.prototype.$$initSubrideMarker = function() {
    this.subrideMarker = new CityMarker(this.subrideLocationLatLng, true);
    this.subrideMarker.addTo(this.map);
    this.subrideMarker.addPopupText(this.settings.subrideMarkerPopupText, true);
    
    that = this;

    this.subrideMarker.on('dragend', function (event) {
        var marker = event.target;
        var position = marker.getLatLng();

        that.$$updateSubridePosition(position);
    });
    
};

EditSubridePage.prototype.isFloat = function(n) {
    n = parseFloat(n);
    return n === Number(n) && n % 1 !== 0;
};

EditSubridePage.prototype.$$updateSubridePosition = function(position) {
    $(this.settings.subrideLatitudeInputSelector).val(position.lat);
    $(this.settings.subrideLongitudeInputSelector).val(position.lng);
};
