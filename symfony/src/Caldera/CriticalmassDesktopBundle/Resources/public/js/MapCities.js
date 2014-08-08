MapCities = function(map)
{
    this.map = map;
    this.layerGroup = L.layerGroup();
    this.layerGroup.addTo(this.map);
};

MapCities.prototype.map = null;
MapCities.prototype.markersArray = new Array();
MapCities.prototype.layerGroup = null;

MapCities.prototype.drawCityMarkers = function()
{
    var cityIcon = L.icon({
        iconUrl: '/bundles/calderacriticalmasscore/images/marker/criticalmassblue.png',
        iconSize: [25, 41],
        iconAnchor: [13, 41],
        popupAnchor: [0, -36],
        shadowUrl: '/bundles/calderacriticalmasscore/images/marker/defaultshadow.png',
        shadowSize: [41, 41],
        shadowAnchor: [13, 41]
    });

    var locationIcon = L.icon({
        iconUrl: '/bundles/calderacriticalmasscore/images/marker/criticalmassred.png',
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
        var marker = null;

        if (ride != null && ride.getHasLocation())
        {
            latLng = [ride.getLatitude(), ride.getLongitude()];
            marker = L.marker(latLng, { riseOnHover: true, icon: locationIcon, citySlug: slug });
        }
        else
        {
            latLng = [city.getLatitude(), city.getLongitude()];
            marker = L.marker(latLng, { riseOnHover: true, icon: cityIcon, citySlug: slug });
        }

        marker.on('click', function()
        {
            showCityInfo(this.options.citySlug);
        });

        marker.addTo(this.layerGroup);

        this.markersArray[slug] = marker;
    }

    CallbackHell.executeEventListener('cityRideMarkersDrawn');
};

MapCities.prototype.openRideCityPopup = function(citySlug)
{
    this.markersArray[citySlug].openPopup();
};