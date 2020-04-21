define(['CriticalService', 'Map', 'Container', 'RideEntity', 'CityEntity', 'jquery.dataTables'], function (CriticalService) {
    PromotionPage = function () {
        this._CriticalService = CriticalService;

        this._initContainer();
        this._initMap();
        this._initTable();
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

    PromotionPage.prototype._initTable = function () {
        const table = $('#ride-table').DataTable({
            'paging': false,
            'searching': false
        });
    };

    PromotionPage.prototype.loadRides = function (apiQuery) {
        const that = this;

        $.get('/api/ride?' + apiQuery, function (data) {
            for (var index in data) {
                const rideEntity = that._CriticalService.factory.createRide(data[index]);

                that._rideContainer.addEntity(rideEntity);
            }
        });
    };

    PromotionPage.prototype.initMapView = function (centerLatitude, centerLongitude, zoomLevel) {
        //var bounds = this._rideContainer.getBounds();
        //this._map.fitBounds(bounds);
        this._map.setView(L.latLng(centerLatitude, centerLongitude), zoomLevel);
    };

    return PromotionPage;
});
