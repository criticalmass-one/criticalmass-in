define(['leaflet'], function() {

    Container = function () {
        this._list = [];
        this._layer = L.featureGroup();
    };

    Container.prototype._list = null;
    Container.prototype._layer = null;

    Container.prototype.add = function (entity) {
        this._list.push(entity);
    };

    Container.prototype.addTo = function (map) {
        for (index = 0; index < this._list.length; ++index) {
            this._list[index].addTo(this._layer);
        }

        this._layer.addTo(map.map);
    };

    Container.prototype.isEmpty = function () {
        return this._list.length == 0;
    };

    Container.prototype.countEntities = function () {
        return this._list.length;
    };

    Container.prototype.addControl = function(layerArray, title) {
        layerArray[title] = this._layer;
    };

    Container.prototype.getBounds = function () {
        return this._layer.getBounds();
    };

    Container.prototype.getEntity = function (index) {
        return this._list[index];
    };

    Container.prototype.add_layer = function (index) {
        this._list[index].addTo(this._layer);
    };

    Container.prototype.remove_layer = function (index) {
        return this._list[index].remove_layer(this._layer);
    };

    Container.prototype.snapTo = function (map, polyline) {
        for (var index in this._list) {
            this._list[index].snapTo(map.map, polyline);
        }
    };

    Container.prototype.addEvent = function (type, callback) {
        for (var index in this._list) {
            this._list[index].addEvent(type, callback);
        }
    };

    return Container;
});