define(['jquery', 'jquery-tablesorter'], function () {
    var SortableTable = function (context) {
        this.context = context;

        this._init();
    };

    SortableTable.prototype._init = function () {
        var options = {
            dateFormat: 'dd/MM/yyyy hh:mm'
        };

        $(this.context).tablesorter(options);
    };

    return SortableTable;
});
