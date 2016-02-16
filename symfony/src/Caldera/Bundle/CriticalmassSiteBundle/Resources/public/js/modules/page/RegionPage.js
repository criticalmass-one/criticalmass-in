define(['Map', 'Container', 'CityEntity'], function() {
    RegionPage = function () {
        this._initContainer();
        this._initMap();
    };

    RegionPage.prototype._map = null;
    RegionPage.prototype._cityContainer = null;

    RegionPage.prototype._initContainer = function() {
        this._cityContainer = new Container();
    };

    RegionPage.prototype._initMap = function() {
        this._map = new Map('map', []);

        this._cityContainer.addToMap(this._map);
    };

    RegionPage.prototype.addCity = function(city, title, slug, description, latitude, longitude) {
        var cityEntity = new CityEntity(city, title, slug, description, latitude, longitude);

        this._cityContainer.addEntity(cityEntity);

        return cityEntity;
    };

    RegionPage.prototype.setFocus = function() {
        var bounds = this._cityContainer.getBounds();
        this._map.fitBounds(bounds);
    };

    return RegionPage;
});
