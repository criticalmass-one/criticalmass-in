define(['CriticalService', 'Map', 'Container', 'CityEntity', 'IncidentEntity', 'NoLocationRideEntity', 'EventEntity', 'MapLayerControl', 'MapLocationControl', 'leaflet-hash', 'Modal', 'CloseModalButton'], function (CriticalService) {
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
    CyclewaysIncidentPage.prototype._incidentContainer = null;
    CyclewaysIncidentPage.prototype._layers = [];
    CyclewaysIncidentPage.prototype._offlineModal = null;

    CyclewaysIncidentPage.prototype._initContainer = function () {
        this._incidentContainer = new Container();
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
        this._incidentContainer.addToMap(this._map);
    };

    CyclewaysIncidentPage.prototype._initLayerControl = function () {
        //this._rideContainer.addToControl(this._layers, 'Tour');

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

    };

    CyclewaysIncidentPage.prototype._onMapChange = function () {
        var url = Routing.generate('caldera_cycleways_incident_source');
        var bounds = this._map.getBounds();

        var northWest = bounds.getNorthWest();
        var southEast = bounds.getSouthEast();

        var data = {
            'northWestLatitude': northWest.lat,
            'northWestLongitude': northWest.lng,
            'southEastLatitude': southEast.lat,
            'southEastLongitude': southEast.lng
        };

        var that = this;

        $.ajax({
            dataType: "json",
            url: url,
            data: data,
            success: function(jsonResult) {
                that._refreshMapEntities(jsonResult);
            }
        });
    };

    CyclewaysIncidentPage.prototype._refreshMapEntities = function(jsonResult) {
        for (var index = 0; index < jsonResult.length; ++index) {
            var incidentJson = jsonResult[index];
            var incident = CriticalService.factory.createIncident(incidentJson);

            incident.addToContainer(this._incidentContainer);
        }
    };

    return CyclewaysIncidentPage;
});
