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
            case 'infrastructure':
                markerIcon = 'fa-bicycle';
                break;
            case 'danger':
                markerIcon = 'fa-exclamation';
                break;
            case 'rage':
                markerIcon = 'fa-bolt';
                break;
            case 'roadworks':
                markerColor = 'green';
                markerIcon = 'fa-wrench';
                break;
            case 'accident':
                markerColor = 'white';
                markerIconColor = 'black';
                markerIcon = 'fa-ambulance';
                break;
            case 'deadly_accident':
                markerColor = 'black';
                markerIcon = 'fa-ambulance';
                break;
            case 'police':
                markerColor = 'blue';
                markerIcon = 'fa-cab';
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