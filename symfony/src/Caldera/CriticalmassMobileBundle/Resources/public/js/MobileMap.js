MobileMap = function(mapIdentifier, city, parentPage)
{
    var this2 = this;

    function initMapView()
    {
        if (CallbackHell.hasEventListenerBeenFired('mapPositionsMarkersDrawn') &&
            CallbackHell.hasEventListenerBeenFired('cityRideMarkersDrawn'))
        {
            this2.initMapView.initView();
        }
    }

    CallbackHell.registerOneTimeEventListener('mapPositionsMarkersDrawn', initMapView);
    CallbackHell.registerOneTimeEventListener('cityRideMarkersDrawn', initMapView);


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


    this.initMap();
    this.positions.startLoop();
    this.cities.drawCityMarkers();
    this.ownPosition.initOwnPosition();
    this.quickLinks.initEventListeners();
    this.messages.startLoop();
    this.mapView.initEventListeners();
    this.initMapEventListeners();
};

MobileMap.prototype = new BaseMap();
MobileMap.prototype.constructor = MobileMap;