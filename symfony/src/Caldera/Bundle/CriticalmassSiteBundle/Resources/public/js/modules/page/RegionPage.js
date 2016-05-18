define(['CriticalService', 'Map', 'Container', 'CityEntity'], function(CriticalService) {
    RegionPage = function () {
        this._CriticalService = CriticalService;

        this._initContainer();
        this._initMap();
    };

    RegionPage.prototype._CriticalService = null;
    RegionPage.prototype._map = null;
    RegionPage.prototype._cityContainer = null;

    RegionPage.prototype._initContainer = function() {
        this._cityContainer = new Container();
    };

    RegionPage.prototype._initMap = function() {
        this._map = new Map('map', []);

        this._cityContainer.addToMap(this._map);
    };

    RegionPage.prototype.addCity = function(cityJson) {
        var cityEntity = this._CriticalService.factory.createCity(cityJson);

        this._cityContainer.addEntity(cityEntity);

        return cityEntity;
    };

    RegionPage.prototype.setFocus = function() {
        var bounds = this._cityContainer.getBounds();
        this._map.fitBounds(bounds);
    };

    return RegionPage;
});
