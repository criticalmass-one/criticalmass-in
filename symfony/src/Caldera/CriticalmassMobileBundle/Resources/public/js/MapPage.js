MapPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;

    this.initMapPageEventListeners();
}

MapPage.prototype = new AppPage();

MapPage.prototype.constructor = MapPage;

MapPage.prototype.map = null;

MapPage.prototype.initMapPageEventListeners = function()
{
    var this2 = this;
    $(document).on('pageshow', '#' + this.pageIdentifier, function() {
        this2.initMap();
    });

    $('#button-force-map-loading').on('click', function()
    {
       this2.initMap();
    });
}

MapPage.prototype.initMap = function()
{
    if (this.map == null)
    {
        this.map = new Map('map', this.getCitySlug(), this);
        $('#section-force-map-loading').remove();
    }
}

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
}

MapPage.prototype.isGpsActivated = function()
{
    var gpsSender = $("select#flip-gps-sender")[0].selectedIndex;

    if (gpsSender == 1)
    {
        return true;
    }

    return false;
}
