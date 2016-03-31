define(['AutoMap'], function() {
    FacebookImportRidePage = function(context, options) {
        this._options = options;

        this._initSideMaps();
    };

    FacebookImportRidePage.prototype._leftMap = null;
    FacebookImportRidePage.prototype._rightMap = null;

    FacebookImportRidePage.prototype._initSideMaps = function() {
        this._leftMap = new AutoMap('left-map');
        this._rightMap = new AutoMap('right-map');
    };

    return FacebookImportRidePage;
});
