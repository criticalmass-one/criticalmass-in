define(['leaflet', 'leaflet-markercluster', 'Container', 'leaflet-extramarkers'], function () {
    IncidentContainer = function () {
        this._list = [];
        this._layer = L.markerClusterGroup({
            showCoverageOnHover: false,
            iconCreateFunction: function (cluster) {
                return L.ExtraMarkers.icon({
                    icon: 'fa-camera',
                    markerColor: 'yellow',
                    shape: 'square',
                    prefix: 'fa'
                });
            }
        });
    };

    IncidentContainer.prototype = new Container();
    IncidentContainer.prototype.constructor = IncidentContainer;

    return IncidentContainer;
});
