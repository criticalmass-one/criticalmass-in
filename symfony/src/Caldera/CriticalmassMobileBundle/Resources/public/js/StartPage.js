StartPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;

    this.initCityList();
    this.initEventListeners();
    this.initMenuUserStatus();
}

StartPage.prototype = new AppPage();
StartPage.prototype.constructor = StartPage;

StartPage.prototype.initCityList = function()
{
    if (!localStorage.cityListData)
    {
        CityFactory.storeAllCities();
    }

    var cities = JSON.parse(localStorage.cityListData).cities;

    for (index in cities)
    {
        var city = cities[index];
        var cityId = 'city-' + city.id + '-' + city.slug;
        var listItem = document.createElement('li');

        $(listItem).attr('data-cityslug', city.slug);
        $(listItem).attr('id', cityId);

        var linkItem = document.createElement('a');

        $(linkItem).append('<img src="http://www.criticalmass.local/bundles/calderacriticalmassmobile/images/criticalmass-bikefist-100.png" />')
        $(linkItem).append('<h3>' + city.title + '</h3>');

        if (city.description != null)
        {
            $(linkItem).append('<p>' + city.description + '</p>');
        }

        $(listItem).append(linkItem);

        $('#cityList').append(listItem);

        var this2 = this;

        $('#' + cityId).click(function()
        {
            var newCitySlug = $(this).attr('data-cityslug');
            this2.switchCityBySlug(newCitySlug);
        });
    }

    $('#cityList').listview('refresh');
}