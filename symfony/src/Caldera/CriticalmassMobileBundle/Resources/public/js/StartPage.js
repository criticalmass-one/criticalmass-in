StartPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;

    this.loadAllCities();
    this.initEventListeners();
    this.initMenuUserStatus();
}

StartPage.prototype = new AppPage();
StartPage.prototype.constructor = StartPage;

StartPage.prototype.loadAllCities = function()
{
    if (sessionStorage.cityListData)
    {
        this.createCityList(JSON.parse(sessionStorage.cityListData));
    }
    else
    {
        $.ajax({
            type: 'GET',
            url: UrlFactory.getApiPrefix() + 'cities/listall',
            cache: false,
            context: this,
            success: function(data)
            {
                sessionStorage.cityListData = JSON.stringify(data);
                this.createCityList(data);
            }
        });
    }
}

StartPage.prototype.createCityList = function(data)
{
    for (index in data.cities)
    {
        var city = data.cities[index];
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

        // TODO Mit echtem Binding wird das hier schöner
        var this2 = this;

        $('#' + cityId).click(function()
        {
            var newCitySlug = $(this).attr('data-cityslug');
            this2.switchCity(newCitySlug);
        });
    }

    $('#cityList').listview('refresh');
}