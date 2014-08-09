Map = function(identifier)
{
    this.identifier = identifier;
};

Map.prototype.identifier = null;
Map.prototype.map = null;

Map.prototype.initMap = function()
{
    this.map = L.map(this.identifier, { zoomControl: false, attributionControl: false });

    this.mapPositions = new MapPositions(this.map);
    this.mapPositions.startLoop();

    this.mapCities = new MapCities(this.map);
    this.mapCities.drawCityMarkers();

    this.mapTileLayer = new MapTileLayer(this.map);
    this.mapTileLayer.init();

    this.mapView = new MapView(this.map);
    this.mapView.initEventListeners();

    this.initMapView = new InitMapView(this.map, this);
    this.initMapView.initView();

    this.mapSidebar = new MapSidebar(this.map, 'sidebar');
    this.mapSidebar.init();

    var zoomControl = L.control.zoom({
        position: "topright"
    }).addTo(this.map);

    var heatmapGroup = L.layerGroup([L.tileLayer("https://www.criticalmass.cm/images/heatmap/ae5e6d7e21c2936051a06a0f2f40661e/{z}/{x}/{y}.png", {
        maxZoom: 18
    })]);

    var layerControl = L.control.groupedLayers(this.mapTileLayer.tileLayers, {
        "Critical Mass": {
            "Städte": this.mapCities.layerGroup,
            "Teilnehmer": this.mapPositions.layerGroup,
            "Heatmaps": heatmapGroup
        }
    }, {
        collapsed: false
    });

    layerControl.addTo(this.map);
};