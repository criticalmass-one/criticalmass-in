define(['Factory'], function() {
    CriticalService = function() {

    };

    CriticalService.prototype._map = null;
    CriticalService.prototype.factory = require('Factory');

    CriticalService.prototype.setMap = function(map) {
        this._map = map;
    };

    CriticalService.prototype.getMap = function(map) {
        return this._map;
    };

    return new CriticalService;
});
