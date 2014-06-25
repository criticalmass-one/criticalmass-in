TileLayerFactory = function()
{

};

TileLayerFactory.convertObjectToTileLayer = function(objectData)
{
    var tileLayer = new TileLayer();

    tileLayer.setId(objectData.id);
    tileLayer.setTitle(objectData.title);
    tileLayer.setAddress(objectData.address);
    tileLayer.setAttributation(objectData.attributation);
    tileLayer.setStandard(objectData.standard);

    return tileLayer;
};

TileLayerFactory.getTileLayers = function()
{
    if (!sessionStorage.tileLayerListData)
    {
        return null;
    }

    var tileLayerList = JSON.parse(sessionStorage.tileLayerListData);
    var resultArray = new Array();

    for (var index in tileLayerList)
    {
        resultArray.push(this.convertObjectToTileLayer(tileLayerList[index]));
    }

    return resultArray;
};

TileLayerFactory.getTileLayerById = function(tileLayerId)
{
    if (!sessionStorage.tileLayerListData)
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
    if (!sessionStorage.tileLayerListData)
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
    if (!sessionStorage.tileLayerListData) or (sessionStorage.tileLayerListData == null)
    {
        $.ajax({
            type: 'GET',
            async: false,
            url: UrlFactory.getApiPrefix() + 'tilelayer/listtilelayers',
            cache: false,
            context: this,
            success: function(data)
            {
                localStorage.tileLayerListData = JSON.stringify(data);
                CallbackHell.executeEventListener('tileLayerListRefreshed');
            }
        });
    }
};