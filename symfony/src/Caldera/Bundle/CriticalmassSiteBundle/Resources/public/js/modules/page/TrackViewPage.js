define(['Map', 'Track'], function() {
    TrackViewPage = function(context, options) {

    };

    TrackViewPage.prototype._map = null;
    TrackViewPage.prototype._track = null;
    TrackViewPage.prototype._polylineLatLngs = null;
    TrackViewPage.prototype._colorRed = null;
    TrackViewPage.prototype._colorGreen = null;
    TrackViewPage.prototype._colorBlue = null;

    TrackViewPage.prototype.setPolylineLatLngs = function(polylineLatLngs) {
        this._polylineLatLngs = polylineLatLngs;
    };

    TrackViewPage.prototype.setColor = function(colorRed, colorGreen, colorBlue) {
        this._colorRed = colorRed;
        this._colorGreen = colorGreen;
        this._colorBlue = colorBlue;
    };

    TrackViewPage.prototype.init = function() {
        this._initMap();
        this._initTrack();
    };

    TrackViewPage.prototype._initMap = function() {
        this._map = new Map('map');
    };

    TrackViewPage.prototype._initTrack = function() {
        this._track = new Track();
        this._track.setPolyline(this._polylineLatLngs, this._colorRed, this._colorGreen, this._colorBlue);
        this._track.addTo(this._map.map);

        this._map.fitBounds(this._track.getBounds());
    };

    return TrackViewPage;
});