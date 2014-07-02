StartPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;

    this.initStartPageEventListeners();
};

StartPage.prototype = new AppPage();
StartPage.prototype.constructor = StartPage;

StartPage.prototype.initStartPageEventListeners = function()
{

};

StartPage.prototype.initPage = function()
{
    var cities = CityFactory.getAllCities();

    for (index in cities)
    {
        var city = cities[index];
        var cityId = 'city-' + city.id + '-' + city.slug;
        var listItem = document.createElement('li');

        $(listItem).attr('data-cityslug', city.slug);
        $(listItem).attr('id', cityId);

        var linkItem = document.createElement('a');

        $(linkItem).append('<h3>' + city.title + '</h3>');

        if (city.description != null)
        {
            $(linkItem).append('<p>' + city.description + '</p>');
        }

        var ride = RideFactory.getRideFromStorageBySlug(city.slug);

        if (ride)
        {
            var dateTime = ride.getDateTime();

            $(linkItem).append('<p class="ui-li-aside">' + dateTime.getDate() + '.' + (dateTime.getMonth() + 1) + '.' + dateTime.getFullYear() + ' ' + dateTime.getHours() + '.' + (dateTime.getMinutes() < 10 ? '0' + dateTime.getMinutes() : dateTime.getMinutes()) + ' Uhr</p>');
        }

        $(listItem).append(linkItem);

        $('#cityList').append(listItem);

        var this2 = this;

        $('#' + cityId).click(function()
        {
            var newCitySlug = $(this).attr('data-cityslug');
            this2.switchCityBySlug(newCitySlug);

            $(":mobile-pagecontainer").pagecontainer("change", "#mapPage");
        });


        $('#cityList').listview('refresh');
    }
};