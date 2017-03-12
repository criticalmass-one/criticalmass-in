define(['Marker', 'IncidentMarkerIcon'], function () {
    IncidentMarker = function (latLng, incidentType, dangerLevel) {
        this._latLng = latLng;
        this._incidentType = incidentType;
        this._dangerLevel = dangerLevel;
        this._incidentMarkerIcon = new IncidentMarkerIcon();
    };

    IncidentMarker.prototype = new Marker();
    IncidentMarker.prototype.constructor = IncidentMarker;

    IncidentMarker.prototype._incidentType = null;
    IncidentMarker.prototype._dangerLevel = null;

    IncidentMarker.prototype._initIcon = function () {
        this._icon = this._incidentMarkerIcon.createMarkerIcon(this._incidentType, this._dangerLevel);
    };

    return IncidentMarker;
});