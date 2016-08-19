define(['CriticalService'], function(CriticalService) {
    LiveFrontPage = function (context, options) {
        this._CriticalService = CriticalService;

        this._options = options;

        this._init();
    };

    LiveFrontPage.prototype._CriticalService = null;

    LiveFrontPage.prototype._init = function() {
        var that = this;

        $('button#ride-on').on('click', function(event) {
            event.preventDefault();

            that._selectRide();
        })
    };

    LiveFrontPage.prototype._selectRide = function() {
        var slug = $( "select#select-ride option:selected" ).data('city-slug');

        var url = Routing.generate('caldera_criticalmass_live_live_city', { citySlug: slug }, true);

        window.location.replace(url);
    };

    return LiveFrontPage;
});
