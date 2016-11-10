define(['Container', 'localforage'], function () {
    LocalContainer = function () {
        this._list = [];
        this._layer = L.featureGroup();
    };

    LocalContainer.prototype = new Container();
    LocalContainer.prototype.constructor = LocalContainer;


    /**
     * Adds an entity to this container.
     *
     * The entity will be placed in our _list as well as in our _layer.
     *
     * @author maltehuebner
     * @since 2015-01-19
     * @param entity
     * @param index
     * @returns {Container} this
     */
    LocalContainer.prototype.addEntity = function (entity, index) {
        if (typeof index === 'undefined') {
            this._list.push(entity);
        } else {
            this._list[index] = entity;

            this._indexList.push(index);
        }

        entity.addToLayer(this._layer);
        ++this._counter;

        return this;
    };

    LocalContainer.prototype.removeEntity = function (index) {
        this._list[index].removeFromLayer(this._layer);
        delete this._list[index];

        var indexListIndex = this._indexList.indexOf(index);
        this._indexList.splice(indexListIndex, 1);

        --this._counter;
    };

    LocalContainer.prototype.removeIndexedEntityFromLayer = function (index) {
        var entity = this._list[index];

        entity.removeFromLayer(this._layer);
    };

    LocalContainer.prototype.addIndexedEntityToLayer = function (index) {
        var entity = this._list[index];

        entity.addToLayer(this._layer);
    };

    LocalContainer.prototype.isIndexedEntityInLayer = function (index) {
        var entity = this._list[index];
        var layer = entity.getLayer();

        return this._layer.hasLayer(layer);
    };

    LocalContainer.prototype.toggleIndexEntityInLayer = function (index) {
        if (this.isIndexedEntityInLayer(index)) {
            this.removeIndexedEntityFromLayer(index);
        } else {
            this.addIndexedEntityToLayer(index);
        }
    };

    /**
     *
     /**
     * Add the elements of this container to a map.
     *
     * @author maltehuebner
     * @since 2015-01-19
     * @param map
     * @returns {Container} this
     */
    LocalContainer.prototype.addToMap = function (map) {
        map.addLayer(this._layer);

        return this;
    };

    /**
     * Returns true if this container is empty.
     *
     * @author maltehuebner
     * @since 2015-01-19
     * @returns {boolean}
     */
    LocalContainer.prototype.isEmpty = function () {
        return this._counter == 0;
    };

    /**
     * Returns the number of entities of this container.
     *
     * @author maltehuebner
     * @since 2015-01-19
     * @returns {*}
     */
    LocalContainer.prototype.countEntities = function () {
        return this._counter;
    };

    /**
     * Adds elements of this container to a leaflet layercontrol array.
     *
     * @author maltehuebner
     * @since 2015-01-19
     * @param layerArray
     * @param title
     */
    LocalContainer.prototype.addToControl = function (layerArray, title) {
        layerArray[title] = this._layer;
    };

    /**
     * Return the bounds of the layer in which the entities of this container are placed.
     *
     * @author maltehuebner
     * @since 2015-01-19
     * @returns {*}
     */
    LocalContainer.prototype.getBounds = function () {
        return this._layer.getBounds();
    };

    /**
     * @author maltehuebner
     * @since 2015-01-19
     * @param index
     * @returns {*}
     */
    LocalContainer.prototype.getEntity = function (index) {
        var entity = this._list[index];

        if (entity != undefined) {
            return entity;
        }

        return null;
    };

    LocalContainer.prototype.hasEntity = function (index) {
        return (index in this._list);
    };

    LocalContainer.prototype.getLayer = function () {
        return this._layer;
    };

    LocalContainer.prototype.getList = function () {
        return this._list;
    };

    LocalContainer.prototype.getIndexList = function () {
        return this._indexList;
    };

    LocalContainer.prototype.snapTo = function (map, polyline) {
        for (var index in this._list) {
            this._list[index].snapTo(map.map, polyline);
        }
    };

    LocalContainer.prototype.addEvent = function (type, callback) {
        for (var index in this._list) {
            this._list[index].addEvent(type, callback);
        }
    };

    LocalContainer.prototype.getPreviousIndex = function (entityIndex) {
        var keys = Object.keys(this._list);
        var previousIndex = null;

        for (var keyIndex in keys) {
            var currentIndex = parseInt(keys[keyIndex]);

            if (!previousIndex && currentIndex < parseInt(entityIndex)) {
                previousIndex = currentIndex;
            } else if (previousIndex < currentIndex && currentIndex < parseInt(entityIndex)) {
                previousIndex = currentIndex;
            }
        }

        return previousIndex;
    };

    LocalContainer.prototype.getPreviousEntity = function (entityIndex) {
        return this.getEntity(this.getPreviousIndex(entityIndex));
    };

    LocalContainer.prototype.getNextIndex = function (entityIndex) {
        var keys = Object.keys(this._list);
        var nextIndex = null;

        for (var keyIndex in keys) {
            var currentIndex = parseInt(keys[keyIndex]);

            if (!nextIndex && currentIndex > parseInt(entityIndex)) {
                nextIndex = currentIndex;
            } else if (nextIndex > currentIndex && currentIndex > parseInt(entityIndex)) {
                nextIndex = currentIndex;
            }
        }

        return nextIndex;
    };

    LocalContainer.prototype.getNextEntity = function (entityIndex) {
        return this.getEntity(this.getNextIndex(entityIndex));
    };

    return LocalContainer;
});
