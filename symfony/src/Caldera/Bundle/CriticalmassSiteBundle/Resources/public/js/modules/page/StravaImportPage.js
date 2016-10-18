define(['Map', 'leaflet-polyline'], function () {
    StravaImportPage = function (settings) {
        this._init();
    };

    StravaImportPage.prototype._init = function () {
        $('.preview-map').each(function (index, value) {
            var map = new Map($(value).attr('id'));

            var polylineString = $(value).data('polyline');

            var polyline = L.Polyline.fromEncoded(polylineString);
            polyline.addTo(map.map);

            map.map.fitBounds(polyline);
        });
    };

    return StravaImportPage;
});