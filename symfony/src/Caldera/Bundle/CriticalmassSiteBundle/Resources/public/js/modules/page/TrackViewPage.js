define(['CriticalService', 'Map'], function(CriticalService) {
    TrackViewPage = function(context, options) {
        this._initMap();

        this._CriticalService = CriticalService;
    };

    TrackViewPage.prototype._map = null;
    TrackViewPage.prototype._track = null;
    TrackViewPage.prototype._CriticalService = null;

    TrackViewPage.prototype.addTrack = function(trackJson) {
        this._track = this._CriticalService.factory.createTrack(trackJson);

        this._track.addToMap(this._map);
    };

    TrackViewPage.prototype.setColor = function(colorRed, colorGreen, colorBlue) {
        this._track.setColor(colorRed, colorGreen, colorBlue);
    };

    TrackViewPage.prototype._initMap = function() {
        this._map = new Map('map');
    };

    TrackViewPage.prototype.focus = function() {
        this._map.fitBounds(this._track.getBounds());
    };

    return TrackViewPage;
});