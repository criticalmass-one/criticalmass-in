define(['CriticalService', 'Map', 'Container', 'RideEntity', 'CityEntity'], function (CriticalService) {
    PromotionPage = function () {
        this._CriticalService = CriticalService;

        this._initContainer();
        this._initMap();
    };

    PromotionPage.prototype._CriticalService = null;
    PromotionPage.prototype._map = null;
    PromotionPage.prototype._rideContainer = null;

    PromotionPage.prototype._initContainer = function () {
        this._rideContainer = new Container();
    };

    PromotionPage.prototype._initMap = function () {
        this._map = new Map('map', []);

        this._rideContainer.addToMap(this._map);

        this._CriticalService.setMap(this._map);
    };

    PromotionPage.prototype.addRide = function (rideJson) {
        var rideEntity = this._CriticalService.factory.createRide(rideJson);

        this._rideContainer.addEntity(rideEntity);

        return rideEntity;
    };

    PromotionPage.prototype.setFocus = function () {
        var bounds = this._rideContainer.getBounds();
        this._map.fitBounds(bounds);
    };

    return PromotionPage;
});
