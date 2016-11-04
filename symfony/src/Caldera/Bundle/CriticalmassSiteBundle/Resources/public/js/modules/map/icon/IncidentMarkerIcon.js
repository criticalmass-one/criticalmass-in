define(['leaflet', 'leaflet-extramarkers'], function () {
    IncidentMarkerIcon = function () {
    };

    IncidentMarkerIcon.prototype.createMarkerIcon = function(incidentType, dangerLevel) {
        var markerColor = 'blue';
        var markerIcon = 'fa-bomb';
        var markerIconColor = 'white';

        switch (dangerLevel) {
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

        switch (incidentType) {
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

        return L.ExtraMarkers.icon({
            icon: markerIcon,
            markerColor: markerColor,
            iconColor: markerIconColor,
            shape: 'square',
            prefix: 'fa'
        });
    };

    return IncidentMarkerIcon;
});