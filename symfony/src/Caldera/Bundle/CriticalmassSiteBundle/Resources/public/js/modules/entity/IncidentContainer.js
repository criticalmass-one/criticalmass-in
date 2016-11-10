define(['leaflet', 'leaflet-markercluster', 'Container', 'leaflet-extramarkers'], function () {
    IncidentContainer = function () {
        this._list = [];
        this._layer = L.markerClusterGroup({
            showCoverageOnHover: false,
            iconCreateFunction: function(cluster) {
                return L.divIcon({ className: 'cluster-counter-outer', html: '<div class="cluster-counter-inner">' + cluster.getChildCount() + '</div>' });
            }
        });
    };

    IncidentContainer.prototype = new Container();
    IncidentContainer.prototype.constructor = IncidentContainer;

    return IncidentContainer;
});
