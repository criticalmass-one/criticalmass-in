define(['CriticalService', 'Map', 'Container', 'CityEntity', 'LiveRideEntity', 'NoLocationRideEntity', 'EventEntity', 'MapLayerControl', 'MapLocationControl', 'leaflet-hash', 'Modal', 'CloseModalButton'], function(CriticalService) {
    CalderaCityMapPage = function (context, options) {
        this._CriticalService = CriticalService;

        this._options = options;

        this._initContainer();
        this._initMap();
        this._initLayers();
        this._initLayerControl();
        this._initLocationControl();
    };

    CalderaCityMapPage.prototype._CriticalService = null;
    CalderaCityMapPage.prototype._options = null;
    CalderaCityMapPage.prototype._map = null;
    CalderaCityMapPage.prototype._hash = null;
    CalderaCityMapPage.prototype._rideContainer = null;
    CalderaCityMapPage.prototype._cityContainer = null;
    CalderaCityMapPage.prototype._eventContainer = null;
    CalderaCityMapPage.prototype._layers = [];
    CalderaCityMapPage.prototype._offlineModal = null;

    CalderaCityMapPage.prototype._initContainer = function() {
        this._rideContainer = new Container();
        this._eventContainer = new Container();
        this._cityContainer = new Container();
    };

    CalderaCityMapPage.prototype._initMap = function() {
        var height = $('nav#navigation').css('height');

        if (!height) {
            height = $('nav').css('height');
        }

        $('#map').css('top', height);

        this._map = new Map('map', []);

        this._hash = new L.Hash(this._map.map);

        this._CriticalService.setMap(this._map);

        this._map.setView([54, 10], 13);
    };

    CalderaCityMapPage.prototype._initLayers = function() {
        this._rideContainer.addToMap(this._map);
        this._cityContainer.addToMap(this._map);
        this._eventContainer.addToMap(this._map);
    };

    CalderaCityMapPage.prototype._initLayerControl = function() {
        this._rideContainer.addToControl(this._layers, 'Tour');

        this._layerControl = new MapLayerControl();
        this._layerControl.setLayers(this._layers);
        this._layerControl.init();
        this._layerControl.addTo(this._map);
    };

    CalderaCityMapPage.prototype._initLocationControl = function() {
        this._locationControl = new MapLocationControl();
        this._locationControl.init();
        this._locationControl.addTo(this._map);
    };

    CalderaCityMapPage.prototype.setFocus = function() {
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

    CalderaCityMapPage.prototype.initIncidentSource = function() {

    };

    return CalderaCityMapPage;
});
