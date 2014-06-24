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
    if (!localStorage.tileLayerListData)
    {
        return null;
    }

    var tileLayerList = JSON.parse(localStorage.tileLayerListData);
    var resultArray = new Array();

    for (var index in tileLayerList)
    {
        resultArray.push(this.convertObjectToTileLayer(tileLayerList[index]));
    }

    return resultArray;
};

TileLayerFactory.getTileLayerById = function(tileLayerId)
{
    if (!localStorage.tileLayerListData)
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
    if (!localStorage.tileLayerListData)
    {
        return null;
    }

    var tileLayerList = this.getTileLayers();

    for (var index in tileLayerList)
    {
        if (tileLayerList[index].getId() == 2)
        {
            return tileLayerList[index];
        }
    }

    return null;
};

TileLayerFactory.storeAllTileLayers = function()
{
    if (!localStorage.tileLayerListData)
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
            }
        });
    }
};