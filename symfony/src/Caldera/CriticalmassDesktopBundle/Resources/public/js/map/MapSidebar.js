MapSidebar = function(map, identifier)
{
    this.map = map;
    this.identifier = identifier;
};

MapSidebar.prototype.map = null;
MapSidebar.prototype.identifier = null;

MapSidebar.prototype.sidebar = null;

MapSidebar.prototype.init = function()
{
    var cities = CityFactory.getAllCities();

    for (var index in cities)
    {
        var city = cities[index];
        var html = '<tr class="cityRow" data-cityslug="' + city.getCitySlug() + '" style="cursor: pointer;"><td class="cityName">' + city.getCity() + '<i class="fa fa-chevron-right pull-right"></i></td></tr>';

        $('#cityList').append(html);
    }

    $('.cityRow').on('click', function()
    {
        showCityInfo($(this).data('cityslug'));
    });

    this.sidebar = L.control.sidebar(this.identifier, {
        closeButton: true,
        position: "left"
    });
    this.sidebar.addTo(this.map);
    /*.on("shown", function () {
     getViewport();
     }).on("hidden", function () {
     getViewport();
     }).*/

    if (document.body.clientWidth > 767)
    {
        this.sidebar.show();
    }
};