define(['jquery', 'jquery-tablesorter'], function () {
    var SortableTable = function (context, settings) {
        this.context = context;
        this.settings = settings;

        this._init();
    };

    SortableTable.prototype._init = function () {
        $(this.context).tablesorter();
    };

    return SortableTable;
});
