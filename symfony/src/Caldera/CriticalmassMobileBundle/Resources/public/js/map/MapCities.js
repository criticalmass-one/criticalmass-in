MapCities = function(map)
{
    this.map = map;
};

MapCities.prototype.map = null;

MapCities.prototype.cityMarkersArray = [];

MapCities.prototype.drawCityMarkers = function()
{
    var criticalmassIcon = L.icon({
        iconUrl: '/bundles/calderacriticalmasscore/images/marker/criticalmassblue.png',
        iconSize: [25, 41],
        iconAnchor: [13, 41],
        popupAnchor: [0, -36],
        shadowUrl: '/bundles/calderacriticalmasscore/images/marker/defaultshadow.png',
        shadowSize: [41, 41],
        shadowAnchor: [13, 41]
    });

    var cities = CityFactory.getAllCities();

    for (slug in cities)
    {
        var city = cities[slug];

        var marker = L.marker([city.getLatitude(), city.getLongitude()], { riseOnHover: true, icon: criticalmassIcon });
        marker.bindPopup(city.getTitle());
        marker.addTo(this.map.map);

        this.cityMarkersArray[slug] = marker;
    }
};