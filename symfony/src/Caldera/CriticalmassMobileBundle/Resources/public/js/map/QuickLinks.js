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
        this2.map.ownPosition.panToOwnPosition();
    });

    $('#quicklinkLatestPosition').on('click', function()
    {
        this2.map.positions.panToLatestPosition();
    });
};