define(['CriticalService', 'Geocoding'], function (CriticalService) {

    CyclewaysLocationSearch = function (context, options) {
        this._CriticalService = CriticalService;
        this._geocoding = new Geocoding();

        this._initEvents();
    };

    CyclewaysLocationSearch.prototype._CriticalService = CriticalService;
    CyclewaysLocationSearch.prototype._geocoding = null;

    CyclewaysLocationSearch.prototype._initEvents = function () {
        $('button#search-location').on('click', this._searchLocation.bind(this));
    };

    CyclewaysLocationSearch.prototype._searchLocation = function () {
        var location = $('#search-input').val();

        this._geocoding.searchPhrase(location, this._searchResult.bind(this));
    };

    CyclewaysLocationSearch.prototype._searchResult = function (result) {
        var southWest = L.latLng(result.boundingbox[1], result.boundingbox[2]),
            northEast = L.latLng(result.boundingbox[0], result.boundingbox[3]),
            bounds = L.latLngBounds(southWest, northEast);

        this._CriticalService.getMap().fitBounds(bounds);
    };

    return CyclewaysLocationSearch;
});