define(['CriticalService', 'Map', 'IncidentContainer', 'CityEntity', 'IncidentEntity', 'MapLayerControl', 'MapLocationControl', 'leaflet-hash', 'Modal', 'CloseModalButton'], function (CriticalService) {
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
        this._incidentContainer = new IncidentContainer();
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

    CyclewaysIncidentPage.prototype.setFocus = function (latitude, longitude) {
        if (!window.location.hash) {
            var latLng = L.latLng(latitude, longitude);

            this._map.setView(latLng, 10);
        }
    };

    CyclewaysIncidentPage.prototype._onMapChange = function () {
        var url = Routing.generate('caldera_cycleways_incident_source');
        var bounds = this._map.getBounds();

        var northWest = bounds.getNorthWest();
        var southEast = bounds.getSouthEast();

        this._incidentContainer.getIndexList(function (knownIndizes) {
            var that = this;

            var data = {
                'northWestLatitude': northWest.lat,
                'northWestLongitude': northWest.lng,
                'southEastLatitude': southEast.lat,
                'southEastLongitude': southEast.lng,
                'knownIndizes': knownIndizes
            };

            $.ajax({
                dataType: 'json',
                url: url,
                data: data,
                method: 'post',
                success: function(jsonResult) {
                    that._refreshMapEntities(jsonResult);
                }
            });
        }.bind(this));

        this._refreshNavigationLinks();
    };

    CyclewaysIncidentPage.prototype._refreshMapEntities = function(jsonResult) {
        var that = this;

        for (var index = 0; index < jsonResult.length; ++index) {
            var incidentJson = jsonResult[index];
            var incident = CriticalService.factory.createIncident(incidentJson);
            incident.addToContainer(this._incidentContainer, incident.getId());
        }
    };

    CyclewaysIncidentPage.prototype._refreshNavigationLinks = function() {
        var hash = window.location.hash;

        if (hash) {
            $nav = $('#navigation-add-incident');
            var oldHref = $nav.attr('href');
            var oldHrefWoHash = oldHref.split('#')[0];

            var newHref = oldHrefWoHash + hash;

            $nav.attr('href', newHref);
        }
    };

    return CyclewaysIncidentPage;
});
