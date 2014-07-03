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
        var cityId = 'city-' + city.getId() + '-' + city.getCitySlug();

        var cityHTML = '<li id="' + cityId + '" data-citySlug="' + city.getCitySlug() + '">';
        cityHTML += '<a>';
        cityHTML += '<h3>' + city.getTitle() + '</h3>';

        if (city.description != null)
        {
            cityHTML += '<p>' + city.getDescription() + '</p>';
        }

        var ride = RideFactory.getRideFromStorageBySlug(city.getCitySlug());

        if (ride)
        {
            cityHTML += '<p class="ui-li-aside">' + ride.getFormattedDateTime() + '</p>';
        }

        cityHTML += '</a></li>';

        $('#cityList').append(cityHTML);

        var this2 = this;

        $('#' + cityId).click(function()
        {
            var newCitySlug = $(this).attr('data-cityslug');
            this2.switchCityBySlug(newCitySlug);

            $(":mobile-pagecontainer").pagecontainer("change", "#mapPage");

            function mapSwitchCallback()
            {
                var mapPage = PageDispatcher.getPage('mapPage');
                mapPage.map.cities.panToRideLocation();
                mapPage.map.cities.openRideCityPopup(newCitySlug);
            }

            if (!CallbackHell.hasEventListenerBeenFired('mapViewInitialised'))
            {
                CallbackHell.registerEventListener('mapViewInitialised', mapSwitchCallback);
            }
            else
            {
                mapSwitchCallback();
            }
        });


        $('#cityList').listview('refresh');
    }
};