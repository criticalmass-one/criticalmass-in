define(['jquery', 'jquery.dataTables'], function ($) {
    var HeatmapListPage = function (context) {
        this.cityListTableSelector = context;

        this._init();
    };

    HeatmapListPage.prototype._init = function () {
        const table = $(this.cityListTableSelector).DataTable({
            'paging': false
        });
    };

    return HeatmapListPage;
});