define([], function() {
    ViewPhotoPage = function() {
        this._installNavigation();
    };

    ViewPhotoPage.prototype._nextPhotoUrl = null;
    ViewPhotoPage.prototype._previousPhotoUrl = null;

    ViewPhotoPage.prototype.setNextPhotoUrl = function(nextPhotoUrl) {
        this._nextPhotoUrl = nextPhotoUrl;
    };

    ViewPhotoPage.prototype.setPreviousPhotoUrl = function(previousPhotoUrl) {
        this._previousPhotoUrl = previousPhotoUrl;
    };

    ViewPhotoPage.prototype._installNavigation = function() {
        var that = this;

        $('body').keydown(function(e) {
            if ((e.keyCode || e.which) == 37 && that._previousPhotoUrl) {
                $(location).attr('href', that._previousPhotoUrl);
            }

            if ((e.keyCode || e.which) == 39 && that._nextPhotoUrl) {
                $(location).attr('href', that._nextPhotoUrl);
            }
        });
    };

    return ViewPhotoPage;
});