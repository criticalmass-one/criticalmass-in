define(['leaflet'], function() {
    BaseEntity = function () {

    };

    BaseEntity.prototype.hasLocation = function() {
        return true;
    };



    BaseEntity.prototype.removeFromLayer = function(markerLayer) {
        markerLayer.removeLayer(this._marker);
    };

    BaseEntity.prototype.addToContainer = function(container, index) {
        container.addEntity(this, index);
    };

    return BaseEntity;
});