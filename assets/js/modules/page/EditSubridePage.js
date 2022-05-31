define(['CriticalService', 'Map', 'CityEntity', 'SubrideMarker', 'RideEntity'], function (CriticalService) {

    var EditSubridePage = function (context, options) {
        this._settings = $.extend(this._defaults, options);

        this._CriticalService = CriticalService;
    };

    EditSubridePage.prototype._defaults = {
        mapContainerId: 'map',
        subrideLatitudeInputSelector: '#subride_latitude',
        subrideLongitudeInputSelector: '#subride_longitude',
        popupText: 'Zieh mich auf den Treffpunkt der Mini-Mass!'
    };

    EditSubridePage.prototype._map = null;
    EditSubridePage.prototype._city = null;
    EditSubridePage.prototype._ride = null;
    EditSubridePage.prototype._$latitudeInput = null;
    EditSubridePage.prototype._$longitudeInput = null;


    EditSubridePage.prototype.init = function () {
        this._initMap();
        this._initView();
        this._initInputFields();
        this._initMarker();
        this._initMarkerEvent();
    };

    EditSubridePage.prototype.addCity = function (cityJson) {
        this._city = this._CriticalService.factory.createCity(cityJson);
    };

    EditSubridePage.prototype.addRide = function (rideJson) {
        this._ride = this._CriticalService.factory.createRide(rideJson);
    };

    EditSubridePage.prototype._initMap = function () {
        this._map = new Map(this._settings.mapContainerId);

        this._city.addToMap(this._map);
        this._ride.addToMap(this._map);
    };

    EditSubridePage.prototype._initView = function () {
        var latLng = null;

        if (this._ride.getLocation() && this._ride.getLatitude() && this._ride.getLongitude()) {
            latLng = this._ride.getLatLng();
        } else {
            latLng = this._city.getLatLng();
        }

        this._map.setView(latLng, 13);
    };

    EditSubridePage.prototype._initInputFields = function () {
        this._$latitudeInput = $(this._settings.subrideLatitudeInputSelector);
        this._$longitudeInput = $(this._settings.subrideLongitudeInputSelector);
    };

    EditSubridePage.prototype._getInputLatLng = function () {
        var latitude = this._$latitudeInput.val();
        var longitude = this._$longitudeInput.val();

        return [latitude, longitude];
    };

    EditSubridePage.prototype._updateInput = function () {
        var latLng = this._marker.getLatLng();

        this._$latitudeInput.val(latLng.lat);
        this._$longitudeInput.val(latLng.lng);
    };

    EditSubridePage.prototype._subrideHasLocation = function () {
        var latitude = this._$latitudeInput.val();
        var longitude = this._$longitudeInput.val();

        return (latitude != 0 && longitude != null);
    };

    EditSubridePage.prototype._initMarker = function (latitude, longitude) {
        var latLng = null;

        if (this._subrideHasLocation()) {
            latLng = this._getInputLatLng();
        } else if (this._ride.location() && this._ride.getLatitude() && this._ride.getLongitude()) {
            latLng = this._ride.getLatLng();
        } else {
            latLng = this._city.getLatLng();
        }

        this._marker = new SubrideMarker(latLng, true);
        this._marker.addToMap(this._map);
        this._marker.addPopupText(this._settings.popupText, true);
    };

    EditSubridePage.prototype._initMarkerEvent = function () {
        var that = this;

        this._marker.on('dragend', function () {
            that._updateInput();
        });
    };

    return EditSubridePage;
});