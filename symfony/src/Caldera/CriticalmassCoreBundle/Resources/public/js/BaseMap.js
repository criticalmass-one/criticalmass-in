BaseMap = function()
{

};

BaseMap.prototype.map = null;
BaseMap.prototype.tileLayer = null;

BaseMap.prototype.initMap = function()
{
    this.map = L.map(this.mapIdentifier);

    var tileLayer = TileLayerFactory.getStandardTileLayer();
    this.setTileLayer(tileLayer);
};

BaseMap.prototype.parentPage = null;
BaseMap.prototype.mapIdentifier = null;

BaseMap.prototype.setViewLatLngZoom = function(latitude, longitude, zoom)
{
    this.map.panTo([latitude, longitude]);
    this.mapView.refreshOverridingMapPosition();
};

BaseMap.prototype.initMapEventListeners = function(ajaxResultData)
{
    var this2 = this;

    this.map.on('dragstart', function()
    {
        this2.positions.stopAutoFollowing();
    });
};

BaseMap.prototype.switchCity = function(newCitySlug)
{
    this.switchTileLayer(this.parentPage.getCitySlug(), newCitySlug);

    this.parentPage.switchCityBySlug(newCitySlug);
};

BaseMap.prototype.switchTileLayer = function(oldCitySlug, newCitySlug)
{
    var oldCity = CityFactory.getCityFromStorageBySlug(oldCitySlug);
    var newCity = CityFactory.getCityFromStorageBySlug(newCitySlug);

    if (oldCity.getTileLayerAddress() != newCity.getTileLayerAddress())
    {
        this.map.removeLayer(this.tileLayer);

        this.tileLayer = L.tileLayer(newCity.getTileLayerAddress(), {
            attribution: newCity.getTileLayerAttributation(),
            detectRetina: true
        });

        this.tileLayer.addTo(this.map);
    }
};

BaseMap.prototype.setTileLayer = function(tileLayer)
{
    if (this.tileLayer)
    {
        this.map.removeLayer(this.tileLayer);
    }

    this.tileLayer = L.tileLayer(tileLayer.getAddress(), {
        attribution: tileLayer.getAttribution()
    });

    _paq.push(['trackEvent', 'switchTileLayer', tileLayer.getTitle()]);

    this.tileLayer.addTo(this.map);
};