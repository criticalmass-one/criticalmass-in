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

    /**
     * Adds an entity to this container.
     *
     * The entity will be placed in our _list as well as in our _layer.
     *
     * @author maltehuebner
     * @since 2015-01-19
     * @param entity
     * @returns {Container} this
     */
    Container.prototype.addEntity = function (entity) {
        this._list.push(entity);

        entity.addToLayer(this._layer);

        return this;
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
        return this._list.length == 0;
    };

    /**
     * Returns the number of entities of this container.
     *
     * @author maltehuebner
     * @since 2015-01-19
     * @returns {*}
     */
    Container.prototype.countEntities = function () {
        return this._list.length;
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
        return this._list[index];
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