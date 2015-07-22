MapPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;
}

MapPage.prototype = new AppPage();

MapPage.prototype.constructor = MapPage;

MapPage.prototype.map = null;

MapPage.prototype.positionSender = null;

MapPage.prototype.initPage = function()
{
    if (this.map == null)
    {
        this.map = new MobileMap('map', this.getCitySlug(), this);
    }

    this.positionSender = new PositionSender(this);

    var tileLayers = TileLayerFactory.getTileLayers();

    for (var index in tileLayers)
    {
        $('#select-tilelayer').append('<option value="' + tileLayers[index].getId() + '" ' + (tileLayers[index].getStandard() ? ' selected="true"' : '') + (!tileLayers[index].isAvailable() ? ' disabled="true"' : '') + '>' + tileLayers[index].getTitle() + '</option>');
    }

    $('#select-tilelayer').selectmenu('refresh');

    var this2 = this;
    $('#select-tilelayer').on('change', function() {
        var tileLayerId = $('#select-tilelayer option:selected').val();

        var tileLayer = TileLayerFactory.getTileLayerById(tileLayerId);
        this2.map.setTileLayer(tileLayer);
    });
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

MapPage.prototype.isOwnPositionActivated = function()
{
    return $("select#flip-show-ownPosition")[0].selectedIndex;
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