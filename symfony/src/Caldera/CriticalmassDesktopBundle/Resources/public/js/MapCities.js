MapCities = function(map)
{
    this.map = map;
};

MapCities.prototype.map = null;
MapCities.prototype.markersArray = new Array();

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
        var ride = RideFactory.getRideFromStorageBySlug(slug);
        var latLng = null;

        if (ride != null && ride.getHasLocation())
        {
            latLng = [ride.getLatitude(), ride.getLongitude()];
        }
        else
        {
            latLng = [city.getLatitude(), city.getLongitude()];
        }

        var marker = L.marker(latLng, { riseOnHover: true, icon: criticalmassIcon, citySlug: slug });

        marker.on('click', function()
        {
            showCityInfo(this.options.citySlug);
        });

        marker.addTo(this.map);

        this.markersArray[slug] = marker;
    }

    CallbackHell.executeEventListener('cityRideMarkersDrawn');
};

MapCities.prototype.openRideCityPopup = function(citySlug)
{
    this.markersArray[citySlug].openPopup();
};