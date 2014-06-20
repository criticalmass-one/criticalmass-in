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

    this.ownPosition.showOwnPosition();
    this.quickLinks.initEventListeners();
    this.messages.startLoop();
    this.mapView.initEventListeners();
    this.initMapEventListeners();
};

Map.prototype.initMap = function()
{
    this.map = L.map(this.mapIdentifier);

    //https://{s}.tiles.mapbox.com/v3/maltehuebner.ii27p08l/{z}/{x}/{y}.png
    //https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png
    L.tileLayer('https://{s}.tiles.mapbox.com/v3/maltehuebner.ii27p08l/{z}/{x}/{y}.png', {
        attribution: '<a href="http://www.mapbox.com/about/maps/" target="_blank">Terms &amp; Feedback</a>'
    }).addTo(this.map);
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
    this.parentPage.switchCityBySlug(newCitySlug);
};