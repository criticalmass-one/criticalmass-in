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
}

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