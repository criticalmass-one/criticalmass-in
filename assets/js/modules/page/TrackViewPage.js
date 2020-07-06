define(['Map', 'Polyline.encoded'], function () {
    TrackViewPage = function () {
    };

    TrackViewPage.prototype._map = null;

    TrackViewPage.prototype.loadTrack = function (trackId) {
        const trackApiUrl = Routing.generate('caldera_criticalmass_rest_track_view', {
            trackId: trackId
        });

        const that = this;
        var request = new XMLHttpRequest();
        request.open('GET', trackApiUrl, true);

        request.onload = function () {
            if (request.status >= 200 && request.status < 400) {
                that._map = new Map('map');

                const data = JSON.parse(request.responseText);

                const polyline = L.Polyline.fromEncoded(data.polylineString);
                polyline.addTo(that._map.map);
            }
        };

        request.send();
    };

    return TrackViewPage;
});