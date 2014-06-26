MapCities = function(map)
{
    this.map = map;
};

MapCities.prototype.map = null;

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
        var popupHTML = '';
        var latLng = null;

        if (ride != null && ride.getHasLocation())
        {
            latLng = [ride.getLatitude(), ride.getLongitude()];
            popupHTML += '<h3>' + ride.getTitle() + '</h3>';
            popupHTML += '<p>Nächste Tour: ' + ride.getFormattedDateTime() + '<br />Treffpunkt: ' + ride.getLocation() + '</p>';
            popupHTML += '<p>' + ride.getDescription() + '</p>';
        }
        else
        if (ride != null)
        {
            latLng = [city.getLatitude(), city.getLongitude()];
            popupHTML += '<h3>' + ride.getTitle() + '</h3>';
            popupHTML += '<p>Nächste Tour: ' + ride.getFormattedDateTime() + '</p>';
            popupHTML += '<p>' + ride.getDescription() + '</p>';
        }
        else
        {
            latLng = [city.getLatitude(), city.getLongitude()];
            popupHTML += '<h3>' + city.getTitle() + '</h3><p>' + city.getDescription() + '</p>';
        }

        var marker = L.marker(latLng, { riseOnHover: true, icon: criticalmassIcon, citySlug: slug });
        marker.bindPopup(popupHTML);

        var this2 = this;
        marker.on('click', function(e)
        {
            _paq.push(['trackEvent', 'markerCityRide', 'click']);

            this2.map.positions.stopAutoFollowing();
            this2.map.switchCity(this.options.citySlug);
        });

        marker.addTo(this.map.map);
    }

    CallbackHell.executeEventListener('cityRideMarkersDrawn');
};

MapCities.prototype.panToRideLocation = function()
{
    var citySlug = this.map.parentPage.getCitySlug();

    var city = CityFactory.getCityFromStorageBySlug(citySlug);
    var ride = RideFactory.getRideFromStorageBySlug(citySlug);

    if (ride != null && ride.getHasLocation())
    {
        this.map.map.panTo(ride.getLatLng());

        _paq.push(['trackEvent', 'panTo', 'ride']);
    }
    else
    {
        this.map.map.panTo(city.getLatLng());

        _paq.push(['trackEvent', 'panTo', 'city']);
    }
};