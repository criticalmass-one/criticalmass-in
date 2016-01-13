define([], function() {
    var SortableTable = function (context, settings) {
        this.context = context;
        this.settings = settings;

        this._init();
    };

    SortableTable.prototype._init = function () {
        var $button = $('#' + this.settings.buttonId);
        var that = this;
        var sortOptionList = $('.sortOption');

        sortOptionList.each(function (n) {
            var $sortOption = $(sortOptionList[n]);

            $sortOption.on('click', function () {
                that._prepareSort($sortOption.attr('data-sortAttribute'));
            });
        });
    };

    SortableTable.prototype._prepareSort = function (sortAttribute) {
        _paq.push(['trackEvent', this.settings.tableId, sortAttribute]);

        if (sortAttribute == 'RideLocation' || sortAttribute == 'cityLocation') {
            this._sortByDistance(sortAttribute);
        }

        var sortFunction = function (a, b) {
            var sortAttributeName = 'data-' + sortAttribute;

            return ($(a).attr(sortAttributeName) > $(b).attr(sortAttributeName) ? 1 : -1);
        };

        this._doSort(sortFunction);
    };

    SortableTable.prototype._sortByDistance = function () {
        var that = this;

        function successCallback(geolocationResult) {
            var latitude = geolocationResult.coords.latitude;
            var longitude = geolocationResult.coords.longitude;

            var sortFunction = function (a, b) {
                var distanceA = Math.sqrt(Math.pow(latitude - $(a).data('latitude'), 2) + Math.pow(longitude - $(a).data('longitude'), 2));
                var distanceB = Math.sqrt(Math.pow(latitude - $(b).data('latitude'), 2) + Math.pow(longitude - $(b).data('longitude'), 2));

                return distanceA - distanceB;
            };

            that._doSort(sortFunction);
        }

        navigator.geolocation.getCurrentPosition(successCallback);
    };

    SortableTable.prototype._doSort = function (sortFunction) {
        var $table = $('#' + this.settings.tableId);

        var $tableHeader = $table.find('thead');
        var $tableBody = $table.find('tbody');

        var tableHeaderContent = $tableHeader.html();

        $tableHeader.remove();

        $tableBody.find('tr').sort(sortFunction).map(function () {
            return $(this).closest($tableBody.find('tr'));
        }).each(function (_, container) {
            $(container).parent().append(container);
        });

        $table.prepend('<thead>' + tableHeaderContent + '</thead>');
    };

    return SortableTable;
});