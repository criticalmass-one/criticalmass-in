define(['DrawMap', 'leaflet-polyline'], function() {
    IncidentEditPage = function () {
        this._initMap();
        this._initDrawableStuff();
    };

    IncidentEditPage.prototype._map = null;

    IncidentEditPage.prototype._initMap = function() {
        this._map = new DrawMap('map');
    };

    IncidentEditPage.prototype._initDrawableStuff = function() {
        // Initialise the FeatureGroup to store editable layers
        var drawnItems = new L.FeatureGroup();
        this._map.map.addLayer(drawnItems);

// Initialise the draw control and pass it the FeatureGroup of editable layers
        var drawControl = new L.Control.Draw({
            edit: {
                featureGroup: drawnItems
            },
            draw: {
                polyline: false,
                rectangle: false,
                circle: false
            }
        });

        this._map.map.addControl(drawControl);

        var that = this;

        this._map.map.on('draw:created', function (e) {
            var type = e.layerType,
                layer = e.layer;

            if (type === 'marker') {
                alert('foooo');
                // Do marker specific actions
            }

            var latLngList = layer.getLatLngs();

            var polyline = L.PolylineUtil.encode(latLngList);

            $('#incident_polyline').val(polyline);


            // Do whatever else you need to. (save to db, add to map etc)
            that._map.map.addLayer(layer);
        });
    };

    IncidentEditPage.prototype.setView = function(centerLatLng, zoom) {
        this._map.setView(centerLatLng, zoom);
    };


    return IncidentEditPage;
});
