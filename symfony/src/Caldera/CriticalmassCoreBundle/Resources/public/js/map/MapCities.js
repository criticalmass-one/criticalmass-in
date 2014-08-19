MapCities = function(map)
{
    this.map = map;
};

MapCities.prototype.map = null;
MapCities.prototype.markersArray = new Array();

MapCities.prototype.drawCityMarkers = function()
{
    var criticalmassIcon = L.icon({
        iconUrl: '/images/marker/criticalmassblue.png',
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
        var popupHTML = '';
        var latLng = null;

        if (ride != null && ride.getHasLocation())
        {
            latLng = [ride.getLatitude(), ride.getLongitude()];

            popupHTML += '<h3>' + (ride.getTitle() ? ride.getTitle() : city.getTitle()) + '</h3>';
            popupHTML += '<p>Nächste Tour: ' + ride.getFormattedDateTime() + '<br />Treffpunkt: ' + ride.getLocation() + '</p>';

            if (ride.getDescription())
            {
                popupHTML += '<p>' + ride.getDescription() + '</p>';
            }
        }
        else
        if (ride != null)
        {
            latLng = [city.getLatitude(), city.getLongitude()];

            popupHTML += '<h3>' + (ride.getTitle() ? ride.getTitle() : city.getTitle()) + '</h3>';
            popupHTML += '<p>Nächste Tour: ' + ride.getFormattedDateTime() + '<br /><em>Der Treffpunkt ist noch nicht bekannt.</em></p>';

            if (ride.getDescription())
            {
                popupHTML += '<p>' + ride.getDescription() + '</p>';
            }
        }
        else
        {
            latLng = [city.getLatitude(), city.getLongitude()];
            popupHTML += '<h3>' + city.getTitle() + '</h3>';

            if (city.getDescription())
            {
                popupHTML += '<p>' + city.getDescription() + '</p>';
            }
            else
            {
                popupHTML += '<p>Für diese Stadt liegen momentan keine weiteren Informationen vor :(</p>';
            }
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

        this.markersArray[slug] = marker;
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

MapCities.prototype.openRideCityPopup = function(citySlug)
{
    this.markersArray[citySlug].openPopup();
};