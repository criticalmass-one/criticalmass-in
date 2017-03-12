define(['CriticalService', 'Map', 'Container', 'CityEntity', 'LiveRideEntity', 'NoLocationRideEntity', 'EventEntity', 'MapLayerControl', 'MapLocationControl', 'MapPositions', 'leaflet-hash', 'Modal', 'CloseModalButton'], function (CriticalService) {
    LivePage = function (context, options) {
        this._CriticalService = CriticalService;

        this._options = options;

        this._initContainer();
        this._initMap();
        this._initLive();
        this._initLayers();
        this._initLayerControl();
        this._initLocationControl();
        this._initCallbacks();
        this._startLive();
    };

    LivePage.prototype._CriticalService = null;
    LivePage.prototype._options = null;
    LivePage.prototype._map = null;
    LivePage.prototype._hash = null;
    LivePage.prototype._rideContainer = null;
    LivePage.prototype._cityContainer = null;
    LivePage.prototype._eventContainer = null;
    LivePage.prototype._layers = [];
    LivePage.prototype._offlineModal = null;

    LivePage.prototype._initContainer = function () {
        this._rideContainer = new Container();
        this._eventContainer = new Container();
        this._cityContainer = new Container();
    };

    LivePage.prototype._initMap = function () {
        var height = $('nav#navigation').css('height');

        if (!height) {
            height = $('nav').css('height');
        }

        $('#map').css('top', height);

        this._map = new Map('map', []);

        this._hash = new L.Hash(this._map.map);

        this._CriticalService.setMap(this._map);
    };

    LivePage.prototype._initLive = function () {
        this._mapPositions = new MapPositions(null, this._options);

        this._mapPositions.addToControl(this._layers, 'Teilnehmer');

        this._CriticalService.setMapPositions(this._mapPositions);
    };

    LivePage.prototype._initLayers = function () {
        this._rideContainer.addToMap(this._map);
        this._cityContainer.addToMap(this._map);
        this._eventContainer.addToMap(this._map);
        this._mapPositions.addToMap(this._map);
        //this._map.addLayer(this._rideContainer.getLayer());
        //this._map.addLayer(this._cityContainer.getLayer());
        //this._map.addLayer(this._mapPositions.getLayer());
    };

    LivePage.prototype._initLayerControl = function () {
        this._rideContainer.addToControl(this._layers, 'Tour');
        //this._cityContainer.addToControl(this._layers, 'Städte');

        this._layerControl = new MapLayerControl();
        this._layerControl.setLayers(this._layers);
        this._layerControl.init();
        this._layerControl.addTo(this._map);
    };

    LivePage.prototype._initLocationControl = function () {
        this._locationControl = new MapLocationControl();
        this._locationControl.init();
        this._locationControl.addTo(this._map);
    };

    LivePage.prototype._initCallbacks = function () {
        //this._mapPositions.setOfflineCallback(this.offlineCallback);
    };

    LivePage.prototype.offlineCallback = function () {
        if (this._offlineModal && this._offlineModal.isVisible()) {
            this._offlineModal.show();
        } else {
            this._offlineModal = new Modal();

            this._offlineModal.setSize('md');
            this._offlineModal.setTitle('Ooops');
            this._offlineModal.setBody('Es ist leider ein Problem mit der Übertragung der Positionsdaten aufgetreten. Bitte versuche es gleich noch einmal.');

            this._offlineModal.setButtons([
                new CloseModalButton()
            ]);

            this._offlineModal.show();
        }
    };

    LivePage.prototype.addCity = function (cityJson) {
        var cityEntity = this._CriticalService.factory.createCity(cityJson);

        this._cityContainer.addEntity(cityEntity);

        return cityEntity;
    };

    LivePage.prototype.addRide = function (rideJson) {
        var rideEntity = this._CriticalService.factory.createLiveRide(rideJson);

        this._rideContainer.addEntity(rideEntity);

        return rideEntity;
    };

    LivePage.prototype.addEvent = function (eventJson) {
        var eventEntity = this._CriticalService.factory.createEvent(eventJson);

        this._eventContainer.addEntity(eventEntity);

        return eventEntity;
    };

    LivePage.prototype.addNoLocationRide = function (title, description, latitude, longitude, location, date, time, weatherForecast) {
        var rideEntity = new NoLocationRideEntity(title, description, latitude, longitude, location, date, time, weatherForecast);

        this._rideContainer.addEntity(rideEntity);

        return rideEntity;
    };

    LivePage.prototype._startLive = function () {
        this._mapPositions.start();
    };

    LivePage.prototype.setFocus = function () {
        if (!location.hash) {
            if (this._rideContainer.countEntities() == 1) {
                var ride = this._rideContainer.getEntity(0);
                this._map.setView([ride.getLatitude(), ride.getLongitude()], 12);
            } else if (this._cityContainer.countEntities() > 0) {
                var city = this._cityContainer.getEntity(0);
                this._map.setView([city.getLatitude(), city.getLongitude()], 12);
            } else {
                var bounds = this._rideContainer.getBounds();
                this._map.fitBounds(bounds);
            }
        }
    };

    return LivePage;
});
