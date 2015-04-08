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
    this.initCities();
    this.initEventListeners();
};

MapSidebar.prototype.initCities = function()
{
    var cities = CityFactory.getAllCities();

    for (var index in cities)
    {
        var city = cities[index];
        var html = '<tr class="cityRow" data-cityslug="' + city.getCitySlug() + '" style="cursor: pointer;"><td class="cityName">' + city.getCity() + '<i class="halflings chevron-right pull-right"></i></td></tr>';

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

    if (!citySlug && document.body.clientWidth > 750)
    {
        this.sidebar.show();
    }
};

MapSidebar.prototype.initEventListeners = function()
{
    var this2 = this;

    $('#navigationButtonSidebar').on('click', function()
    {
        this2.sidebar.toggle();
        _paq.push(['trackEvent', 'sidebar', 'toggle']);
    });

    $('#sidebarButtonToggle').on('click', function()
    {
        this2.sidebar.toggle();
        _paq.push(['trackEvent', 'sidebar', 'toggle']);
    });
};