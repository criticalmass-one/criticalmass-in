define(['jquery', 'datatables'], function () {
    var SortableTable = function (context) {
        this.context = context;

        this._init();
    };

    SortableTable.prototype._init = function () {
        $(this.context).DataTable({
            'paging': false
        });
    };

    return SortableTable;
});
