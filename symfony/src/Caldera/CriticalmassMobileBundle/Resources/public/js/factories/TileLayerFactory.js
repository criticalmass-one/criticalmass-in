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

CityFactory.getCityFromStorageBySlug = function(citySlug)
{
    if (!localStorage.cityListData)
    {
        return null;
    }

    var cityList = JSON.parse(localStorage.cityListData);

    for (index in cityList.cities)
    {
        if (cityList.cities[index].slug == citySlug)
        {
            return this.convertObjectToCity(cityList.cities[index]);
        }
    }

    return null;
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