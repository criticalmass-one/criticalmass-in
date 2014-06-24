Map = function(mapIdentifier, city, parentPage)
{
    this.mapIdentifier = mapIdentifier;
    this.city = city;
    this.parentPage = parentPage;

    this.mapView = new MapView(this);
    this.positions = new MapPositions(this);
    this.cities = new MapCities(this);
    this.initMapView = new InitMapView(this);
    this.ownPosition = new OwnPosition(this);
    this.quickLinks = new QuickLinks(this);
    this.messages = new Messages(this);

    var this2 = this;
    CallbackHell.registerOneTimeEventListener('positionLoopStarted', function() { this2.initMapView.initView(); });

    this.initMap();
    this.positions.startLoop();
    this.cities.drawCityMarkers();

    this.ownPosition.initOwnPosition();
    this.quickLinks.initEventListeners();
    this.messages.startLoop();
    this.mapView.initEventListeners();
    this.initMapEventListeners();
};

Map.prototype.map = null;
Map.prototype.tileLayer = null;

Map.prototype.initMap = function()
{
    this.map = L.map(this.mapIdentifier);

    var tileLayer = TileLayerFactory.getStandardTileLayer();
    this.setTileLayer(tileLayer);
};

Map.prototype.parentPage = null;
Map.prototype.mapIdentifier = null;

Map.prototype.setViewLatLngZoom = function(latitude, longitude, zoom)
{
    this.map.panTo([latitude, longitude]);
    this.mapView.refreshOverridingMapPosition();
}

Map.prototype.initMapEventListeners = function(ajaxResultData)
{
    var this2 = this;

    this.map.on('dragstart', function()
    {
        this2.positions.stopAutoFollowing();
    });
};

Map.prototype.switchCity = function(newCitySlug)
{
    this.switchTileLayer(this.parentPage.getCitySlug(), newCitySlug);

    this.parentPage.switchCityBySlug(newCitySlug);
};

Map.prototype.switchTileLayer = function(oldCitySlug, newCitySlug)
{
    var oldCity = CityFactory.getCityFromStorageBySlug(oldCitySlug);
    var newCity = CityFactory.getCityFromStorageBySlug(newCitySlug);

    if (oldCity.getTileLayerAddress() != newCity.getTileLayerAddress())
    {
        this.map.removeLayer(this.tileLayer);

        this.tileLayer = L.tileLayer(newCity.getTileLayerAddress(), {
            attribution: newCity.getTileLayerAttributation()
        });

        this.tileLayer.addTo(this.map);
    }
};

Map.prototype.setTileLayer = function(tileLayer)
{
    if (this.tileLayer)
    {
        this.map.removeLayer(this.tileLayer);
    }

    this.tileLayer = L.tileLayer(tileLayer.getAddress(), {
        attribition: tileLayer.getAttributation()
    });

    _paq.push(['trackEvent', 'switchTileLayer', tileLayer.getTitle()]);

    this.tileLayer.addTo(this.map);
};