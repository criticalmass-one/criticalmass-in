MapPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;
}

MapPage.prototype = new AppPage();

MapPage.prototype.constructor = MapPage;

MapPage.prototype.map = null;

MapPage.prototype.initPage = function(callback)
{
    if (this.map == null)
    {
        this.map = new Map('map', this.getCitySlug(), this);
    }

    if (callback)
    {
        callback(this);
    }
};

MapPage.prototype.refreshGpsGauge = function(quality)
{
    if ((quality >= 0) && (quality <= 25))
    {
        $('#gpsquality').html('sehr gut (' + quality + ' Meter)');
    }
    else if ((quality > 25) && (quality <= 70))
    {
        $('#gpsquality').html('annehmbar (' + quality + ' Meter)');
    }
    else
    {
        $('#gpsquality').html('unbrauchbar (' + quality + ' Meter)');
    }
};

MapPage.prototype.isGpsActivated = function()
{
    return $("select#flip-gps-sender")[0].selectedIndex;
};

MapPage.prototype.autoFollow = false;

MapPage.prototype.isAutoFollow = function()
{
    return this.autoFollow;
};

MapPage.prototype.setAutoFollow = function(autoFollow)
{
    this.autoFollow = autoFollow;
};