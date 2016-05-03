define(['AutoMap', 'leaflet-polyline'], function() {
    TrackListPage = function (settings) {
        this._init();
    };

    TrackListPage.prototype._init = function() {
        $('.preview-map').each(function(index, value) {
            var map = new AutoMap($(value).attr('id'));
        });
    };

    return TrackListPage;
});