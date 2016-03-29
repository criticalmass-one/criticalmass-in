define(['leaflet', 'leaflet-markercluster', 'Container', 'leaflet-extramarkers'], function() {
    ClusterContainer = function () {
        this._list = [];
        this._layer = L.markerClusterGroup({
            showCoverageOnHover: false,
            iconCreateFunction: function(cluster) {
                return L.ExtraMarkers.icon({
                    icon: 'fa-camera',
                    markerColor: 'yellow',
                    shape: 'square',
                    prefix: 'fa'
                });
            }
        });
    };

    ClusterContainer.prototype = new Container();
    ClusterContainer.prototype.constructor = ClusterContainer;

    return ClusterContainer;
});
