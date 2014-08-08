Map = function(identifier)
{
    this.identifier = identifier;

    this.initMapView = new InitMapView(map);
};

Map.prototype.identifier = null;

Map.prototype.initMapView = null;

Map.prototype.initMap = function()
{
    map = L.map(this.identifier, { zoomControl: false, attributionControl: false });

    initMapView.initView();
};