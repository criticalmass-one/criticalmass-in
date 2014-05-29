StartPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;

    this.initEventListeners();
    this.initMenuUserStatus();

    var this2 = this;
    $(document).on('pageshow', '#' + this.pageIdentifier, function() {
        this2.initCityList();
    });
}

StartPage.prototype = new AppPage();
StartPage.prototype.constructor = StartPage;

StartPage.prototype.initPage = false;

StartPage.prototype.initCityList = function()
{
    if (this.initPage == false)
    {
        this.initPage = true;

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
            });


            $('#cityList').listview('refresh');
        }
    }
}