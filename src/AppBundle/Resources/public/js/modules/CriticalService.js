define(['Factory'], function () {
    CriticalService = function () {

    };

    CriticalService.prototype.factory = require('Factory');

    CriticalService.prototype._map = null;

    CriticalService.prototype.setMap = function (map) {
        this._map = map;
    };

    CriticalService.prototype.getMap = function (map) {
        return this._map;
    };

    CriticalService.prototype._mapPositions = null;

    CriticalService.prototype.setMapPositions = function (mapPositions) {
        this._mapPositions = mapPositions;
    };

    CriticalService.prototype.getMapPositions = function (mapPositions) {
        return this._mapPositions;
    };

    return new CriticalService;
});
