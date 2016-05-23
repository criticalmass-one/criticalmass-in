define(['AutoMap'], function() {
    FacebookImportRidePage = function(context, options) {
        this._options = options;

        this._initSideMaps();
        this._initCopyButtons();
    };

    FacebookImportRidePage.prototype._leftMap = null;
    FacebookImportRidePage.prototype._rightMap = null;

    FacebookImportRidePage.prototype._initSideMaps = function() {
        this._leftMap = new AutoMap('left-map');
        this._rightMap = new AutoMap('right-map');
    };

    FacebookImportRidePage.prototype._initCopyButtons = function() {
        $('button.copy-button').on('click', function() {
            $input = $(this).parent().find('.value');

            $row = $(this).parents('.row');

            $col = $row.find('.col-md-4:nth-child(2)');

            $dst = $col.find('.value');

            $dst.val($input.val());
        });
    };

    return FacebookImportRidePage;
});
