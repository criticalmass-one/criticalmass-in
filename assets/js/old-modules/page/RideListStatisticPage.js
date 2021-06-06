define(['jquery', 'dateformat', 'jquery.dataTables'], function ($, dateFormat) {
    var CityRideListPage = function (context) {
        this.rideListTableSelector = context;

        this._init();
    };

    CityRideListPage.prototype._init = function () {
        const table = $(this.rideListTableSelector).DataTable({
            'paging': false,
            'order': [[2, 'desc']], // default sorting by participants
        });
    };

    return CityRideListPage;
});