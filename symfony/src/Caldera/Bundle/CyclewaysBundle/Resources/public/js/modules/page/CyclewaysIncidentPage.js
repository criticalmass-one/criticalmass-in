define(['CriticalService', 'Map', 'Container', 'CityEntity', 'LiveRideEntity', 'NoLocationRideEntity', 'EventEntity', 'MapLayerControl', 'MapLocationControl', 'leaflet-hash', 'Modal', 'CloseModalButton'], function (CriticalService) {
    CyclewaysIncidentPage = function (context, options) {
        this._CriticalService = CriticalService;

        this._options = options;

        this._initContainer();
        this._initMap();
        this._initLayers();
        this._initLayerControl();
        this._initLocationControl();
    };

    CyclewaysIncidentPage.prototype._CriticalService = null;
    CyclewaysIncidentPage.prototype._options = null;
    CyclewaysIncidentPage.prototype._map = null;
    CyclewaysIncidentPage.prototype._hash = null;
    CyclewaysIncidentPage.prototype._rideContainer = null;
    CyclewaysIncidentPage.prototype._cityContainer = null;
    CyclewaysIncidentPage.prototype._eventContainer = null;
    CyclewaysIncidentPage.prototype._layers = [];
    CyclewaysIncidentPage.prototype._offlineModal = null;

    CyclewaysIncidentPage.prototype._initContainer = function () {
        this._rideContainer = new Container();
        this._eventContainer = new Container();
        this._cityContainer = new Container();
    };

    CyclewaysIncidentPage.prototype._initMap = function () {
        var height = $('nav#navigation').css('height');

        if (!height) {
            height = $('nav').css('height');
        }

        $('#map').css('top', height);

        this._map = new Map('map', []);

        this._hash = new L.Hash(this._map.map);

        this._CriticalService.setMap(this._map);

        this._map.setView([54, 10], 13);

        var that = this;

        this._map.on('moveend', this._onMapChange.bind(this));
    };

    CyclewaysIncidentPage.prototype._initLayers = function () {
        this._rideContainer.addToMap(this._map);
        this._cityContainer.addToMap(this._map);
        this._eventContainer.addToMap(this._map);
    };

    CyclewaysIncidentPage.prototype._initLayerControl = function () {
        this._rideContainer.addToControl(this._layers, 'Tour');

        this._layerControl = new MapLayerControl();
        this._layerControl.setLayers(this._layers);
        this._layerControl.init();
        this._layerControl.addTo(this._map);
    };

    CyclewaysIncidentPage.prototype._initLocationControl = function () {
        this._locationControl = new MapLocationControl();
        this._locationControl.init();
        this._locationControl.addTo(this._map);
    };

    CyclewaysIncidentPage.prototype.setFocus = function () {
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

    CyclewaysIncidentPage.prototype._onMapChange = function () {
        var url = Routing.generate('caldera_cycleways_incident_source');
        var data = {

        };

        $.ajax({
            dataType: "json",
            url: url,
            data: data,
            success: function() {
                alert('foo');
            }
        });
        
        console.log(this._map.getBounds().getNorthWest());
    };

    return CyclewaysIncidentPage;
});
