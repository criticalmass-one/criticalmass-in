define(['Map'], function () {
    TrackViewPage = function (context, options) {
        this._initMap();
    };

    TrackViewPage.prototype._map = null;
    TrackViewPage.prototype._track = null;

    TrackViewPage.prototype.loadTrack = function (trackId) {
        const trackApiUrl = Routing.generate('caldera_criticalmass_rest_track_view', {
            trackId: trackId
        });

        $.getJSON(trackApiUrl).ajaxSuccess(function (data) {
            alert(data);
        });
        this._track = this._CriticalService.factory.createTrack(trackJson);

        this._track.addToMap(this._map);
    };

    TrackViewPage.prototype.setColor = function (colorRed, colorGreen, colorBlue) {
        this._track.setColor(colorRed, colorGreen, colorBlue);
    };

    TrackViewPage.prototype._initMap = function () {
        this._map = new Map('map');
    };

    TrackViewPage.prototype.focus = function () {
        this._map.fitBounds(this._track.getBounds());
    };

    return TrackViewPage;
});