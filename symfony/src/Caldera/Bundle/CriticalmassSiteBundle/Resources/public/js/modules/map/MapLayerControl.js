define(['leaflet', 'leaflet-groupedlayer'], function() {
    MapLayerControl = function() {
    };

    MapLayerControl.prototype._layers = null;
    MapLayerControl.prototype._layerControl = null;

    MapLayerControl.prototype.setLayers = function(layers) {
        this._layers = layers;
    };

    MapLayerControl.prototype.getLayers = function() {
        return this._layers;
    };

    MapLayerControl.prototype.init = function() {
        this._layerControl = L.control.groupedLayers({}, {
            'Critical Mass': this._layers
        }, {
            collapsed: true
        });
    };

    MapLayerControl.prototype.addTo = function(map) {
        this._layerControl.addTo(map.map);
    };

    return MapLayerControl;
});