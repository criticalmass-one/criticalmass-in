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
    city.setAutoDetect(objectData.autoDetect);

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

    for (var index in cityList.cities)
    {
        if (cityList.cities[index].slug == citySlug)
        {
            var city = this.convertObjectToCity(cityList.cities[index]);
            return city;
        }
    }

    return null;
};

CityFactory.getCurrentCity = function()
{
    return this.convertJSONToCity(localStorage.currentCity);
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
            url: Url.getApiPrefix() + 'cities/getbyslug/' + citySlug,
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
            url: Url.getApiPrefix() + 'cities/listall',
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

CityFactory.getNearestCity = function()
{
    var this2 = this;

    function successCallback(resultData)
    {
        var latitude = resultData.coords.latitude;
        var longitude = resultData.coords.longitude;
        var minDistance = null;
        var nearestCityIndex = null;
        var index;

        var cityList = JSON.parse(this2.storage);

        for (index in cityList.cities)
        {
            var city = cityList.cities[index];
            var distance = Math.sqrt(Math.pow(city.latitude - latitude, 2) + Math.pow(city.longitude - longitude, 2));

            if ((!minDistance || (distance < minDistance) && city.autoDetect == true))
            {
                minDistance = distance;
                nearestCityIndex = index;
            }
        }

        sessionStorage.currentCity = JSON.stringify(cityList.cities[nearestCityIndex]);
        sessionStorage.citySlug = nearestCityIndex;

        CallbackHell.executeEventListener('findNearestCitySuccess');
        CallbackHell.executeEventListener('findNearestCityFinished');
    }

    function errorCallback(resultData)
    {
        CallbackHell.executeEventListener('findNearestCityFailure');
        CallbackHell.executeEventListener('findNearestCityFinished');
    }

    if (!this.storage)
    {
        return null;
    }

    navigator.geolocation.getCurrentPosition(successCallback, errorCallback, { maximumAge: 15000, timeout: 2500, enableHighAccuracy: false });
};