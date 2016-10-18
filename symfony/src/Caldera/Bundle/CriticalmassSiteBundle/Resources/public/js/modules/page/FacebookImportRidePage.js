define(['AutoMap'], function () {
    FacebookImportRidePage = function (context, options) {
        this._options = options;

        this._initSideMaps();
        this._initCopyButtons();
    };

    FacebookImportRidePage.prototype._leftMap = null;
    FacebookImportRidePage.prototype._rightMap = null;

    FacebookImportRidePage.prototype._initSideMaps = function () {
        this._leftMap = new AutoMap('left-map');
        this._rightMap = new AutoMap('right-map');
    };

    FacebookImportRidePage.prototype._initCopyButtons = function () {
        var that = this;

        $('button.copy-button').on('click', function () {
            $input = $(this).parent().find('.value');

            $row = $(this).parents('.row');

            $col = $row.find('.col-md-4:nth-child(2)');

            $stringInput = $col.find('.string-value');
            $dateSelects = $col.find('.date-value');
            $timeSelects = $col.find('.time-value');

            if ($stringInput) {
                $stringInput.val($input.val());
            }

            if ($dateSelects.length > 0) {
                that._copyDateValue($dateSelects, $input.val());
            }

            if ($timeSelects.length > 0) {
                that._copyTimeValue($timeSelects, $input.val());
            }
        });
    };

    FacebookImportRidePage.prototype._copyValue = function () {

    };

    FacebookImportRidePage.prototype._copyDateValue = function ($timeSelects, value) {
        var date = value.split('.');

        var $day = $timeSelects.find('select:nth-child(1)');
        var $month = $timeSelects.find('select:nth-child(2)');
        var $year = $timeSelects.find('select:nth-child(3)');

        $day.val(parseInt(date[0]));
        $month.val(parseInt(date[1]));
        $year.val(parseInt(date[2]));
    };

    FacebookImportRidePage.prototype._copyTimeValue = function ($timeSelects, value) {
        var time = value.split(':');

        var $hour = $timeSelects.find('select:nth-child(1)');
        var $minute = $timeSelects.find('select:nth-child(2)');

        $hour.val(parseInt(time[0]));
        $minute.val(parseInt(time[1]));
    };


    return FacebookImportRidePage;
});
