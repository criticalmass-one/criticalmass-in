InitMapView = function(map, citySlug)
{
    this.map = map;
    this.citySlug = citySlug;

    this.mapView = new MapView(this.map);
};

InitMapView.prototype.map = null;

InitMapView.prototype.initView = function()
{
    var city = CityFactory.getCityFromStorageBySlug(this.citySlug);
    var ride = RideFactory.getRideFromStorageBySlug(this.citySlug);

    if (this.mapView.hasOverridingMapPosition())
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
        //this.map.cities.openRideCityPopup(citySlug);
    }
    else
    if (citySlug != null)
    {
        this.initWithCity(city);
        //this.map.cities.openRideCityPopup(citySlug);
    }
    else
    {
        this.initStandardView();
    }

    CallbackHell.executeEventListener('mapViewInitialised');
};

InitMapView.prototype.initWithOverride = function()
{
    var mapPositon = this.mapView.getOverridingMapPosition();
    this.map.setView([mapPositon.latitude, mapPositon.longitude], mapPositon.zoomFactor);

    //_paq.push(['trackEvent', 'initView', 'override']);
};

InitMapView.prototype.initWithRide = function(ride)
{
    var latLng = [ride.getLatitude(), ride.getLongitude()];
    this.map.setView(latLng, 12);

    //_paq.push(['trackEvent', 'initView', 'ride']);
};

InitMapView.prototype.initWithCity = function(city)
{
    var latLng = [city.getLatitude(), city.getLongitude()];
    this.map.setView(latLng, 10);

    //_paq.push(['trackEvent', 'initView', 'city']);
};

InitMapView.prototype.initWithPosition = function()
{
    var latestPosition = this.map.positions.getLatestPosition();

    this.map.setView(latestPosition.getLatLng(), 14);
};

InitMapView.prototype.initStandardView = function()
{
    this.map.setView([51.590556, 10.106111], 6);
};