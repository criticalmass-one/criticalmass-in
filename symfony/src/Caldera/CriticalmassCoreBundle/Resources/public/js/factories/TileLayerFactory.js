TileLayerFactory = function()
{

};

TileLayerFactory.storage = sessionStorage.tileLayerListData;

TileLayerFactory.convertObjectToTileLayer = function(objectData)
{
    var tileLayer = new TileLayer();

    tileLayer.setId(objectData.id);
    tileLayer.setTitle(objectData.title);
    tileLayer.setAddress(objectData.address);
    tileLayer.setAttribution(objectData.attribution);
    tileLayer.setStandard(objectData.standard);

    return tileLayer;
};

TileLayerFactory.getTileLayers = function()
{
    if (!this.storage)
    {
        return null;
    }

    var tileLayerList = JSON.parse(this.storage);
    var resultArray = new Array();

    for (var index in tileLayerList)
    {
        resultArray.push(this.convertObjectToTileLayer(tileLayerList[index]));
    }

    return resultArray;
};

TileLayerFactory.getTileLayerById = function(tileLayerId)
{
    if (!this.storage)
    {
        return null;
    }

    var tileLayerList = this.getTileLayers();

    for (var index in tileLayerList)
    {
        if (tileLayerList[index].getId() == tileLayerId)
        {
            return tileLayerList[index];
        }
    }

    return null;
};

TileLayerFactory.getStandardTileLayer = function()
{
    if (!this.storage)
    {
        return null;
    }

    var tileLayerList = this.getTileLayers();
    var standardTileLayer = null;
    var index;

    for (index in tileLayerList)
    {
        if (tileLayerList[index].getStandard() == true)
        {
            standardTileLayer = tileLayerList[index];
        }
    }

    if (!standardTileLayer)
    {
        standardTileLayer = tileLayerList[index];
    }

    return standardTileLayer;
};

TileLayerFactory.storeAllTileLayers = function()
{
    if (!this.storage || this.storage == null)
    {
        $.ajax({
            type: 'GET',
            async: false,
            url: Url.getApiPrefix() + 'tilelayer/listtilelayers',
            cache: false,
            context: this,
            success: function(data)
            {
                this.storage = JSON.stringify(data);
                CallbackHell.executeEventListener('tileLayerListRefreshed');
            }
        });
    }
};

TileLayerFactory.refreshAllStoredTileLayers = function()
{
    this.storage = null;
    this.storeAllTileLayers();
}