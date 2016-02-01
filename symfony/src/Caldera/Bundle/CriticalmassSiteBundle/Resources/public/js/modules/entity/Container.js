define(['leaflet'], function() {

    /**
     * A container manages entities like Cities or Rides to display on a map.
     *
     * @author maltehuebner
     * @since 2015-01-19
     * @constructor
     */
    Container = function () {
        this._list = [];
        this._layer = L.featureGroup();
    };

    /**
     * This list contains the entities of this container.
     * @type {null}
     * @author maltehuebner
     * @since 2015-01-19
     * @private
     */
    Container.prototype._list = null;

    /**
     * This is a leaflet feature group and will contain the "visible results" of the entities of this container. As the
     * _list will hold our javascript objects for city, ride or track entities, the layer will contain those markers or
     * polylines or whatever.
     *
     * @type {null}
     * @author maltehuebner
     * @since 2015-01-19
     * @private
     */
    Container.prototype._layer = null;

    Container.prototype._counter = 0;

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
    Container.prototype.addEntity = function (entity, index) {
        if (typeof index === 'undefined') {
            this._list.push(entity);
        } else {
            this._list[index] = entity;
        }

        entity.addToLayer(this._layer);
        ++this._counter;

        return this;
    };

    Container.prototype.removeEntity = function(index) {
        this._list[index].removeFromLayer(this._layer);
        delete this._list[index];

        --this._counter;
    };

    Container.prototype.removeIndexedEntityFromLayer = function(index) {
        var entity = this._list[index];

        entity.removeFromLayer(this._layer);
    };

    Container.prototype.addIndexedEntityToLayer = function(index) {
        var entity = this._list[index];

        entity.addToLayer(this._layer);
    };

    Container.prototype.isIndexedEntityInLayer = function(index) {
        var entity = this._list[index];
        var layer = entity.getLayer();

        return this._layer.hasLayer(layer);
    };

    Container.prototype.toggleIndexEntityInLayer = function(index) {
        if (this.isIndexedEntityInLayer(index)) {
            alert('is visible');
            this.removeIndexedEntityFromLayer(index);
        } else {
            alert('is not visible');
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
    Container.prototype.addToMap = function(map) {
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
    Container.prototype.isEmpty = function () {
        return this._counter == 0;
    };

    /**
     * Returns the number of entities of this container.
     *
     * @author maltehuebner
     * @since 2015-01-19
     * @returns {*}
     */
    Container.prototype.countEntities = function () {
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
    Container.prototype.addToControl = function(layerArray, title) {
        layerArray[title] = this._layer;
    };

    /**
     * Return the bounds of the layer in which the entities of this container are placed.
     *
     * @author maltehuebner
     * @since 2015-01-19
     * @returns {*}
     */
    Container.prototype.getBounds = function () {
        return this._layer.getBounds();
    };

    /**
     * @author maltehuebner
     * @since 2015-01-19
     * @param index
     * @returns {*}
     */
    Container.prototype.getEntity = function (index) {
        var entity = this._list[index];

        if (entity != undefined) {
            return entity;
        }

        return null;
    };

    Container.prototype.hasEntity = function(index) {
        return (index in this._list);
    };

    Container.prototype.getLayer = function() {
        return this._layer;
    };

    Container.prototype.getList = function() {
        return this._list;
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

    Container.prototype.getPreviousIndex = function(entityIndex) {
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

    Container.prototype.getPreviousEntity = function(entityIndex) {
        return this.getEntity(this.getPreviousIndex(entityIndex));
    };

    Container.prototype.getNextIndex = function(entityIndex) {
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

    Container.prototype.getNextEntity = function(entityIndex) {
        return this.getEntity(this.getNextIndex(entityIndex));
    };

    return Container;
});
