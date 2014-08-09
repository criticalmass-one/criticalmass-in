MapTileLayer = function(map)
{

};

MapTileLayer.prototype.tileLayers = new Array();
MapTileLayer.prototype.standardTileLayer = null;

MapTileLayer.prototype.init = function()
{
    var tileLayerObjects = TileLayerFactory.getTileLayers();

    for (var index in tileLayerObjects)
    {
        var tileLayerObject = tileLayerObjects[index];
        var tileLayer = L.tileLayer(tileLayerObject.getAddress(), {foo: 'bar'});

        this.tileLayers[tileLayerObject.getTitle()] = tileLayer;

        if (tileLayerObject.getStandard())
        {
            this.standardTileLayer = tileLayer;
        }
    }
};