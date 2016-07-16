define(['CriticalService', 'AutoMap', 'PhotoEntity', 'hammerjs'], function(CriticalService) {
    ViewPhotoPage = function() {
        this._installNavigation();
        this._installSwipe();

        this._CriticalService = CriticalService;
    };

    ViewPhotoPage.prototype._CriticalService = null;
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
            if ((e.keyCode || e.which) == 37) {
                that._navigateBackwards();
            }

            if ((e.keyCode || e.which) == 39) {
                that._navigateForwards();
            }
        });
    };

    ViewPhotoPage.prototype._installSwipe = function() {
        var $photo = $('img#photo');
        var mc = new Hammer(document.getElementById('photo'), { velocity: 50 });
        var that = this;
        var triggerWidth = $photo.width() * 0.3;

        mc.on('panleft', function(ev) {
            if (that._nextPhotoUrl) {
                $photo.css({
                    'margin-left': ev.deltaX + "px"
                });

                if (ev.deltaX < -triggerWidth) {
                    that._navigateForwards();
                }
            }
        });

        mc.on('panright', function(ev) {
            if (that._previousPhotoUrl) {
                $photo.css({
                    'margin-left': ev.deltaX + "px"
                });

                if (ev.deltaX > triggerWidth) {
                    that._navigateBackwards();
                }
            }
        });
    };

    ViewPhotoPage.prototype._navigateBackwards = function() {
        if (this._previousPhotoUrl) {
            $(location).attr('href', this._previousPhotoUrl);
        }
    };

    ViewPhotoPage.prototype._navigateForwards = function() {
        if (this._nextPhotoUrl) {
            $(location).attr('href', this._nextPhotoUrl);
        }
    };

    ViewPhotoPage.prototype.setPhoto = function(photoJson, filename) {
        this._photo = this._CriticalService.factory.createPhoto(photoJson);
        this._photo.setFilename(filename);
    };

    ViewPhotoPage.prototype.initMap = function() {
        this._map = new AutoMap('map');
    };

    return ViewPhotoPage;
});