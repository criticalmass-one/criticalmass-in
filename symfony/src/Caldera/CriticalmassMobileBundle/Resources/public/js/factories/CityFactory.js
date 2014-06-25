CityFactory = function()
{

};

CityFactory.storage = sessionStorage.cityListData;

CityFactory.hasCities = function()
{
    return (this.storage != null) && (this.storage != '');
};

CityFactory.convertJSONToCity = function(jsonData)
{
    var cityObject = JSON.parse(jsonData);

    return this.convertObjectToCity(cityObject);
};

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

    return city;
};

CityFactory.getAllCities = function()
{
    if (!this.storage)
    {
        return null;
    }

    var cityList = JSON.parse(this.storage);
    var resultList = new Array();

    for (index in cityList.cities)
    {
        var city = this.convertObjectToCity(cityList.cities[index]);
        resultList[city.getCitySlug()] = city;
    }

    return resultList;
};


CityFactory.getCityFromStorageBySlug = function(citySlug)
{
    if (!this.storage)
    {
        return null;
    }

    var cityList = JSON.parse(this.storage);

    for (index in cityList.cities)
    {
        if (cityList.cities[index].slug == citySlug)
        {
            return this.convertObjectToCity(cityList.cities[index]);
        }
    }

    return null;
};

CityFactory.getCityBySlug = function(citySlug)
{
    var city = null;

    if (sessionStorage.city)
    {
        city = sessionStorage.city;
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

                sessionStorage.city = city;
            }
        });
    }

    return city;
};

CityFactory.storeAllCities = function()
{
    if (!this.storage || this.storage == null)
    {
        $.ajax({
            type: 'GET',
            async: false,
            url: UrlFactory.getApiPrefix() + 'cities/listall',
            cache: false,
            context: this,
            success: function(data)
            {
                this.storage = JSON.stringify(data);
                CallbackHell.executeEventListener('cityListRefreshed');
            }
        });
    }
};

CityFactory.refreshAllStoredCities = function()
{
    this.storage = null;
    this.storeAllCities();
};