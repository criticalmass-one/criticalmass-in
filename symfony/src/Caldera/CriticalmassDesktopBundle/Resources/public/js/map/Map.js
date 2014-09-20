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

    var heatmapGroup = L.layerGroup([L.tileLayer("https://www.criticalmass.cm/images/heatmap/globalheatmap/{z}/{x}/{y}.png", {
        maxZoom: 18
    })]);

    var layerControl = L.control.groupedLayers(this.mapTileLayer.tileLayers, {
        "Critical Mass": {
            "Städte": this.mapCities.layerGroup,
            "Teilnehmer": this.mapPositions.layerGroup,
            "Heatmaps": heatmapGroup
        }
    }, {
        collapsed: true
    });

    layerControl.addTo(this.map);

    if (this.mapSidebar.sidebar.isVisible()) {
        this.map.setActiveArea({
            position: "absolute",
            top: "0px",
            left: $(".leaflet-sidebar").css("width"),
            right: "0px",
            height: $("#map").css("height")
        });
    } else {
        this.map.setActiveArea({
            position: "absolute",
            top: "0px",
            left: "0px",
            right: "0px",
            height: $("#map").css("height")
        });
    }
};