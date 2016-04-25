define(['Map', 'Container', 'IncidentEntity'], function() {
    IncidentPage = function () {
        this._initMap();
    };

    IncidentPage.prototype._map = null;
    IncidentPage.prototype._incidentContainer = new Container();

    IncidentPage.prototype._initMap = function() {
        this._map = new Map('map', []);
    };

    IncidentPage.prototype.addIncident = function(title, description, geometryType, incidentType, polyline, expires, visibleFrom, visibleTo) {
        var incidentEntity = new IncidentEntity(title, description, geometryType, incidentType, polyline, expires, visibleFrom, visibleTo);

        incidentEntity.addToContainer(this._incidentContainer);
    };

    IncidentPage.prototype.init = function() {
        this._incidentContainer.addToMap(this._map);
    };

    IncidentPage.prototype.setFocus = function() {
        var bounds = this._incidentContainer.getBounds();
        this._map.fitBounds(bounds);
    };

    return IncidentPage;
});
