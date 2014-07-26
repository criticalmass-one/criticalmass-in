InitMapView = function(map)
{
    this.map = map;
};

InitMapView.prototype.map = null;

InitMapView.prototype.initView = function()
{
    var citySlug = this.map.parentPage.getCitySlug();
    var city = CityFactory.getCityFromStorageBySlug(citySlug);
    var ride = RideFactory.getRideFromStorageBySlug(citySlug);

    if (this.map.mapView.hasOverridingMapPosition())
    {
        this.initWithOverride();
    }
    else
    if (ride != null && ride.isRolling() && this.map.positions.getLatestPosition())
    {
        this.initWithPosition();
    }
    else
    if (ride != null && ride.getHasLocation())
    {
        this.initWithRide(ride);
        this.map.cities.openRideCityPopup(citySlug);
    }
    else
    {
        this.initWithCity(city);
        this.map.cities.openRideCityPopup(citySlug);
    }

    CallbackHell.executeEventListener('mapViewInitialised');
};

InitMapView.prototype.initWithOverride = function()
{
    var mapPositon = this.map.mapView.getOverridingMapPosition();
    this.map.map.setView([mapPositon.latitude, mapPositon.longitude], mapPositon.zoomFactor);

    _paq.push(['trackEvent', 'initView', 'override']);
};

InitMapView.prototype.initWithRide = function(ride)
{
    var latLng = [ride.getLatitude(), ride.getLongitude()];
    this.map.map.setView(latLng, 12);

    _paq.push(['trackEvent', 'initView', 'ride']);
};

InitMapView.prototype.initWithCity = function(city)
{
    var latLng = [city.getLatitude(), city.getLongitude()];
    this.map.map.setView(latLng, 10);

    _paq.push(['trackEvent', 'initView', 'city']);
};

InitMapView.prototype.initWithPosition = function()
{
    var latestPosition = this.map.positions.getLatestPosition();

    this.map.map.setView(latestPosition.getLatLng(), 14);
};