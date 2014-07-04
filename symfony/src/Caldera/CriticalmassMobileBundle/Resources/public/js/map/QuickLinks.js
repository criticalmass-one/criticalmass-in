QuickLinks = function(map)
{
    this.map = map;
};

QuickLinks.prototype.map = null;

QuickLinks.prototype.initEventListeners = function()
{
    var this2 = this;

    $('#quicklinkOwnPosition').on('click', function()
    {
        this2.map.positions.stopAutoFollowing();
        this2.map.ownPosition.panToOwnPosition();
    });

    $('#quicklinkLatestPosition').on('click', function()
    {
        this2.map.positions.panToLatestPosition();
    });

    $('#quicklinkLocation').on('click', function()
    {
        this2.map.positions.stopAutoFollowing();
        this2.map.cities.panToRideLocation();
        this2.map.cities.openRideCityPopup(this2.map.parentPage.getCitySlug());
    });
};