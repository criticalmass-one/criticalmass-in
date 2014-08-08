Map = function(identifier)
{
    this.identifier = identifier;
};

Map.prototype.identifier = null;
Map.prototype.map = null;

Map.prototype.initMapView = null;

Map.prototype.initMap = function()
{
    this.map = L.map(this.identifier, { zoomControl: false, attributionControl: false });

    this.initMapView = new InitMapView(this.map);
    this.initMapView.initView();

    this.mapPositions = new MapPositions(this.map);
    this.mapPositions.startLoop();

    this.mapCities = new MapCities(this.map);
    this.mapCities.drawCityMarkers();

    this.mapView = new MapView(this.map);
    this.mapView.initEventListeners();
};