MapView = function(map)
{
    this.map = map;
};

MapView.prototype.map = null;

MapView.prototype.initEventListeners = function()
{
    var this2 = this;

    this.map.map.on('dragend', function()
    {
        this2.refreshOverridingMapPosition();
    });

    this.map.map.on('zoomend', function()
    {
        this2.refreshOverridingMapPosition();
    });
};

MapView.prototype.getOverridingMapPosition = function()
{
    if (this.hasOverridingMapPosition())
    {
        return JSON.parse(sessionStorage.mapView);
    }

    return null;
};

MapView.prototype.hasOverridingMapPosition = function()
{
    return sessionStorage.mapView && sessionStorage.mapView != null;
};

MapView.prototype.refreshOverridingMapPosition = function()
{
    var view = { latitude: this.map.map.getCenter().lat,
                 longitude: this.map.map.getCenter().lng,
                 zoomFactor: this.map.map.getZoom() };

    sessionStorage.mapView = JSON.stringify(view);
};