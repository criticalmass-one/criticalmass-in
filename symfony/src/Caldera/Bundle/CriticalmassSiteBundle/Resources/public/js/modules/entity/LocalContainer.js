define(['Container', 'localforage'], function () {
    LocalContainer = function () {
        this._list = [];
        this._layer = L.featureGroup();
    };

    LocalContainer.prototype = new Container();
    LocalContainer.prototype.constructor = LocalContainer;

    return LocalContainer;
});
