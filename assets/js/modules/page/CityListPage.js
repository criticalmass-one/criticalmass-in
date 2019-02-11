define(['jquery', 'jquery.dataTables'], function () {
    var CityListPage = function (context) {
        this.$cityListTable = context;

        this._init();
    };

    CityListPage.prototype._init = function () {
        $(this.$cityListTable).DataTable({
            'paging': false
        });
    };

    return CityListPage;
});
