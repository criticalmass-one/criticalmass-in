CityFactory = function()
{

}

CityFactory.convertJSONToCity = function(jsonData)
{
    var cityObject = JSON.parse(jsonData);
    var city = new City();

    city.setId(cityObject.id);
    city.setCitySlug(cityObject.slug);
    city.setCity(cityObject.city);
    city.setTitle(cityObject.title);
    city.setDescription(cityObject.description);
}

CityFactory.convertObjectToCity = function(objectData)
{
    var city = new City();

    city.setId(objectData.id);
    city.setCitySlug(objectData.slug);
    city.setCity(objectData.city);
    city.setTitle(objectData.title);
    city.setDescription(objectData.description);

    return city;
}

CityFactory.getCityFromStorageBySlug = function(citySlug)
{
    if (!sessionStorage.cityListData)
    {
        alert("STORAGE IST LEER");
        return null;
    }

    var cityList = JSON.parse(sessionStorage.cityListData);

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