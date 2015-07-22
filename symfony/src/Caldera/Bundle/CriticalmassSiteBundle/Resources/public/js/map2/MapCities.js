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
        iconUrl: '/images/marker/marker-gray.png',
        iconRetinaUrl: '/images/marker/marker-gray-2x.png',
        iconSize: [25, 41],
        iconAnchor: [13, 41],
        popupAnchor: [0, -36],
        shadowUrl: '/images/marker/defaultshadow.png',
        shadowSize: [41, 41],
        shadowAnchor: [13, 41]
    });

    var locationIcon = L.icon({
        iconUrl: '/images/marker/marker-red.png',
        iconRetinaUrl: '/images/marker/marker-red-2x.png',
        iconSize: [25, 41],
        iconAnchor: [13, 41],
        popupAnchor: [0, -36],
        shadowUrl: '/images/marker/defaultshadow.png',
        shadowSize: [41, 41],
        shadowAnchor: [13, 41]
    });

    var unknownLocationIcon = L.icon({
        iconUrl: '/images/marker/marker-yellow.png',
        iconRetinaUrl: '/images/marker/marker-yellow-2x.png',
        iconSize: [25, 41],
        iconAnchor: [13, 41],
        popupAnchor: [0, -36],
        shadowUrl: '/images/marker/defaultshadow.png',
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
        else if (ride != null && !ride.getHasLocation())
        {
            latLng = [city.getLatitude(), city.getLongitude()];
            marker = L.marker(latLng, { riseOnHover: true, icon: unknownLocationIcon, citySlug: slug });
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