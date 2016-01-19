define(['leaflet'], function() {
    BaseEntity = function () {

    };

    BaseEntity.prototype.hasLocation = function() {
        return true;
    };

    BaseEntity.prototype.addToMap = function(map) {
        if (this.hasLocation()) {
            this._createMarker();

            this._marker.addTo(map.map);
        }
    };

    BaseEntity.prototype.addToLayer = function(markerLayer) {
        if (this.hasLocation()) {
            this._createMarker();

            markerLayer.addLayer(this._marker);
        }
    };

    BaseEntity.prototype.addToContainer = function(container) {
        container.addEntity(this);
    };

    return BaseEntity;
});