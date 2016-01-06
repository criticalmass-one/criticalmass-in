define(['Map', 'Container', 'City', 'Ride', 'MapLayerControl', 'MapLocationControl', 'MapPositions'], function() {
    LivePage = function () {
        this._initContainer();
        this._initMap();
        this._initLive();
        this._initControls();
    };

    LivePage.prototype._map = null;
    LivePage.prototype._rideContainer = null;
    LivePage.prototype._cityContainer = null;
    LivePage.prototype._city = null;
    LivePage.prototype._ride = null;
    LivePage.prototype._layers = [];

    LivePage.prototype.addCity = function(city, title, slug, description, latitude, longitude) {
        this._city = new City(city, title, slug, description, latitude, longitude);

        this._cityContainer.add(this._city);
    };

    LivePage.prototype.addRide = function(title, description, latitude, longitude, location, date, time, weatherForecast) {
        this._ride = new Ride(null, title, description, latitude, longitude, location, date, time, weatherForecast);

        this._rideContainer.add(this._ride);
    };

    LivePage.prototype._initContainer = function() {
        this._rideContainer = new Container();
        this._cityContainer = new Container();
    };

    LivePage.prototype._initMap = function() {
        $('#map').css('top', $('nav#navigation').css('height'));

        this._map = new Map('map', []);
    };

    LivePage.prototype._initControls = function() {
        this._rideContainer.addControl(this._layers, 'Tour');
        this._cityContainer.addControl(this._layers, 'Städte');

        this._layerControl = new MapLayerControl();
        this._layerControl.setLayers(this._layers);
        this._layerControl.init();
        this._layerControl.addTo(this._map);

        this._locationControl = new MapLocationControl();
        this._locationControl.init();
        this._locationControl.addTo(this._map);

        this._map.setView([53.4015275, 9.7984212], 12);
    };

    LivePage.prototype._initLive = function() {
        this._mapPositions = new MapPositions();

        this._mapPositions.addControl(this._layers, 'Teilnehmer');

        this._mapPositions.start();
    };

    return LivePage;
});