define(['Marker'], function () {
    IncidentMarker = function (latLng, incidentType, dangerLevel) {
        this._latLng = latLng;
        this._incidentType = incidentType;
        this._dangerLevel = dangerLevel;
    };

    IncidentMarker.prototype = new Marker();
    IncidentMarker.prototype.constructor = IncidentMarker;

    IncidentMarker.prototype._incidentType = null;
    IncidentMarker.prototype._dangerLevel = null;

    IncidentMarker.prototype._initIcon = function () {
        var markerColor = 'blue';
        var markerIcon = 'fa-bomb';
        var markerIconColor = 'white';

        switch (this._dangerLevel) {
            case 'low':
                markerColor = 'yellow';
                markerIcon = 'fa-exclamation';
                break;
            case 'normal':
                markerColor = 'orange';
                markerIcon = 'fa-exclamation';
                break;
            case 'high':
                markerColor = 'red';
                markerIcon = 'fa-exclamation';
                break;
        }

        switch (this._incidentType) {
            case 'accident':
                markerColor = 'white';
                markerIconColor = 'black';
                markerIcon = 'fa-ambulance';
                break;
            case 'deadly_accident':
                markerColor = 'black';
                markerIcon = 'fa-ambulance';
                break;
            case 'high':
                markerColor = 'red';
                break;
        }

        this._icon = L.ExtraMarkers.icon({
            icon: markerIcon,
            markerColor: markerColor,
            iconColor: markerIconColor,
            shape: 'square',
            prefix: 'fa'
        });
    };

    return IncidentMarker;
});