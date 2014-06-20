CityFactory = function()
{

}

CityFactory.hasCities = function()
{
    return (localStorage.cityListData != null) && (localStorage.cityListData != '');
}

CityFactory.convertJSONToCity = function(jsonData)
{
    var cityObject = JSON.parse(jsonData);

    return this.convertObjectToCity(cityObject);
}

CityFactory.convertObjectToCity = function(objectData)
{
    var city = new City();

    city.setId(objectData.id);
    city.setCitySlug(objectData.slug);
    city.setCity(objectData.city);
    city.setTitle(objectData.title);
    city.setDescription(objectData.description);
    city.setUrl(objectData.url);
    city.setFacebook(objectData.facebook);
    city.setTwitter(objectData.twitter);
    city.setLatitude(objectData.latitude);
    city.setLongitude(objectData.longitude);
    city.setTileLayerAddress(objectData.tileLayerAddress);
    city.setTileLayerAttributation(objectData.tileLayerAttributation);

    return city;
}

CityFactory.getAllCities = function()
{
    if (!localStorage.cityListData)
    {
        return null;
    }

    var cityList = JSON.parse(localStorage.cityListData);
    var resultList = new Array();

    for (index in cityList.cities)
    {
        var city = this.convertObjectToCity(cityList.cities[index]);
        resultList[city.getCitySlug()] = city;
    }

    return resultList;
}


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

CityFactory.getCityBySlug = function(citySlug)
{
    var city = null;

    if (localStorage.city)
    {
        city = localStorage.city;
    }
    else
    {
        $.ajax({
            type: 'GET',
            async: false,
            url: UrlFactory.getApiPrefix() + 'cities/getbyslug/' + citySlug,
            cache: false,
            context: this,
            success: function(ajaxResultData)
            {
                city = new City();
                city.parseAjaxResultData(ajaxResultData.city);

                localStorage.city = city;
            }
        });
    }

    return city;
}

CityFactory.storeAllCities = function(callback)
{
    if (!localStorage.cityListData) or (localStorage.cityListData = null)
    {
        $.ajax({
            type: 'GET',
            async: false,
            url: UrlFactory.getApiPrefix() + 'cities/listall',
            cache: false,
            context: this,
            success: function(data)
            {
                localStorage.cityListData = JSON.stringify(data);
                callback();
            }
        });
    }
}

CityFactory.refreshAllStoredCities = function(callback)
{
    localStorage.cityListData = null;
    this.storeAllCities(callback);
}