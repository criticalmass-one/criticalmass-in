define(['CriticalService', 'Map', 'Container', 'IncidentEntity'], function(CriticalService) {
    IncidentPage = function () {
        this._CriticalService = CriticalService;

        this._initMap();
    };

    IncidentPage.prototype._CriticalService = null;
    IncidentPage.prototype._map = null;
    IncidentPage.prototype._incidentContainer = new Container();

    IncidentPage.prototype._initMap = function() {
        this._map = new Map('map', []);

        this._CriticalService.setMap(this._map);
    };

    IncidentPage.prototype.addIncident = function(id, title, description, geometryType, incidentType, polyline, expires, visibleFrom, visibleTo) {
        var incidentEntity = new IncidentEntity(id, title, description, geometryType, incidentType, polyline, expires, visibleFrom, visibleTo);
        
        incidentEntity.addToContainer(this._incidentContainer, id);
    };

    IncidentPage.prototype.init = function() {
        this._incidentContainer.addToMap(this._map);

        var that = this;

        $('.incident').on('click', function() {
            var incidentId = $(this).data('incident-id');

            that.openIncidentPopup(incidentId);
        })
    };

    IncidentPage.prototype.setFocus = function() {
        var bounds = this._incidentContainer.getBounds();
        this._map.fitBounds(bounds);
    };

    IncidentPage.prototype.openIncidentPopup = function(incidentId) {
        var incident = this._incidentContainer.getEntity(incidentId);

        incident.openPopup();

        this._map.fitBounds(incident.getBounds());
    };

    return IncidentPage;
});
