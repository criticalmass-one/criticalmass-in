define(['Map', 'Container', 'City'], function() {

    var RidePage = function(context, options) {
        this._init();
    };

    RidePage.prototype.map = null;
    RidePage.prototype._rideContainer = null;
    RidePage.prototype._subrideContainer = null;
    RidePage.prototype._city = null;

    RidePage.prototype._init = function() {
        this._initMap();

        this._rideContainer = new Container();
        this._subrideContainer = new Container();
    };

    RidePage.prototype._initMap = function() {
        this.map = new Map('map');

        var m = new LocationMarker([0, 0], false);
        m.addTo(this.map);
    };

    RidePage.prototype.addCity = function(cityName, cityTitle, slug, description, latitude, longitude) {
        this._city = new City(cityName, cityTitle, slug, description, latitude, longitude);

        this._city.addTo(this.map.map);
    };

    RidePage.prototype.addRide = function(city, title, description, latitude, longitude, location, date, time, weatherForecast) {
        var ride = new Ride(this._city, title, description, latitude, longitude, location, date, time, weatherForecast);

        this._rideContainer.add(ride);
    };

    return RidePage;
});