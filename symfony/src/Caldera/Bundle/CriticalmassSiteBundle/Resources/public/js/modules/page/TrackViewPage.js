define(['Map', 'TrackEntity'], function() {
    TrackViewPage = function(context, options) {
        this._initMap();
    };

    TrackViewPage.prototype._map = null;
    TrackViewPage.prototype._track = null;

    TrackViewPage.prototype.addTrack = function(trackId, polyline, colorRed, colorGreen, colorBlue) {
        this._track = new TrackEntity(trackId, polyline, colorRed, colorGreen, colorBlue);

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