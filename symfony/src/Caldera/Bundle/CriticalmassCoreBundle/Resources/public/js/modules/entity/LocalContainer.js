define(['localforage', 'CriticalService', 'Container'], function (localforage, CriticalService) {
    LocalContainer = function () {
        this._storage = localforage.createInstance({
            name: "incidents"
        });

        this._layer = L.featureGroup();
        this._CriticalService = CriticalService;

        this._storage.clear();
        this._restore();
    };

    LocalContainer.prototype = new Container();
    LocalContainer.prototype.constructor = LocalContainer;

    LocalContainer.prototype._storage = null;
    LocalContainer.prototype._CriticalService = null;

    LocalContainer.prototype._restore = function () {
        this._storage.iterate(function(entityJson, key, iterationNumber) {
            var entity = CriticalService.factory.createIncident(entityJson);

            entity.addToLayer(this._layer);
        }.bind(this));
    };

    LocalContainer.prototype.addEntity = function (entity, index) {
        var onFound = function() {

        }.bind(this);

        var onMissing = function () {
            this._storage.setItem(String(index), JSON.stringify(entity));

            entity.addToLayer(this._layer);
        }.bind(this);

        this.hasEntity(index, onFound, onMissing);

        return this;
    };

    LocalContainer.prototype.removeEntity = function (index) {
        this._storage.removeItem(index);
    };

    LocalContainer.prototype.removeIndexedEntityFromLayer = function (index) {
        console.error('not implemented');
    };

    LocalContainer.prototype.addIndexedEntityToLayer = function (index) {
        console.error('not implemented');
    };

    LocalContainer.prototype.isIndexedEntityInLayer = function (index) {
        console.error('not implemented');
    };

    LocalContainer.prototype.isEmpty = function (callback) {
        console.error('not implemented');
    };

    LocalContainer.prototype.countEntities = function (callback) {
        this._storage.length(callback);
    };

    LocalContainer.prototype.getEntity = function (index, callback) {
        this._storage.getItem(index, callback);
    };

    LocalContainer.prototype.hasEntity = function (index, successCallback, errorCallback) {
        this.getIndexList(function(indexArray) {
            if (indexArray.indexOf(String(index)) > -1) {
                successCallback();
            } else {
                errorCallback();
            }
        });
    };

    LocalContainer.prototype.getIndexList = function (callback) {
        this._storage.keys().then(callback);
    };

    LocalContainer.prototype.snapTo = function (map, polyline) {
        this._storage.iterate(function(entity, key, iterationNumber) {
            entity.snapTo(map.map, polyline);
        });
    };

    LocalContainer.prototype.addEvent = function (type, callback) {
        this._storage.iterate(function(entity, key, iterationNumber) {
            entity.addEvent(type, callback);
        });
    };

    LocalContainer.prototype.getPreviousIndex = function (entityIndex) {
        console.error('not implemented');
    };

    LocalContainer.prototype.getNextIndex = function (entityIndex) {
        console.error('not implemented');
    };


    return LocalContainer;
});
