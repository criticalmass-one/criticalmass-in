StartPage = function(pageIdentifier)
{
    this.loadAllCities();

    this.pageIdentifier = pageIdentifier;
}

StartPage.prototype = new AppPage();
StartPage.prototype.constructor = StartPage;

StartPage.prototype.cityList;

StartPage.prototype.loadAllCities = function()
{
    $.ajax({
        type: 'GET',
        url: this.getApiPrefix() + 'cities/listall',
        cache: false,
        context: this,
        success: function(data)
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
    });
}