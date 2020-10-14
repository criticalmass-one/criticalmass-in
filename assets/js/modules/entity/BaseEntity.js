define(['leaflet'], function () {
    BaseEntity = function () {

    };

    BaseEntity.prototype.hasLocation = function () {
        return false;
    };

    BaseEntity.prototype.removeFromLayer = function (markerLayer) {
        markerLayer.removeLayer(this._marker);
    };

    BaseEntity.prototype.addToContainer = function (container, index) {
        container.addEntity(this, index);
    };

    BaseEntity.prototype.getId = function () {
        return this._id;
    };

    return BaseEntity;
});