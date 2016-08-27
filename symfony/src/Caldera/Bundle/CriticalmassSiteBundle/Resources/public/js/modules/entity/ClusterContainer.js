/**
 * @todo: Rename this to PhotoContainer
 *
 */
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

    ClusterContainer.prototype.getPreviousIndex = function(entityIndex) {
        var keys = Object.keys(this._list);
        var previousIndex = null;
        var previousDateTime = null;
        var dateTime = this.getEntity(entityIndex).getDateTime();
        
        for (var keyIndex in keys) {
            var currentIndex = parseInt(keys[keyIndex]);
            var currentDateTime = this.getEntity(currentIndex).getDateTime();

            if (!previousDateTime && currentDateTime < dateTime) {
                previousIndex = currentIndex;
                previousDateTime = currentDateTime;
            } else if (previousDateTime < currentDateTime && currentDateTime < dateTime) {
                previousIndex = currentIndex;
                previousDateTime = currentDateTime;
            }
        }

        return previousIndex;
    };
    
    Container.prototype.getNextIndex = function(entityIndex) {
        var keys = Object.keys(this._list);
        var nextIndex = null;
        var nextDateTime = null;
        var dateTime = this.getEntity(entityIndex).getDateTime();

        for (var keyIndex in keys) {
            var currentIndex = parseInt(keys[keyIndex]);
            var currentDateTime = this.getEntity(currentIndex).getDateTime();

            if (!nextDateTime && currentDateTime > dateTime) {
                nextIndex = currentIndex;
                nextDateTime = currentDateTime;
            } else if (nextDateTime > currentDateTime && currentDateTime > dateTime) {
                nextIndex = currentIndex;
                nextDateTime = currentDateTime;
            }
        }

        return nextIndex;
    };


    return ClusterContainer;
});
