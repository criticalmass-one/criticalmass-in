define(['DrawMap'], function() {
    IncidentEditPage = function () {
        this._map = new DrawMap('map');

        this._map.setView([53, 9], 10);

        // Initialise the FeatureGroup to store editable layers
        var drawnItems = new L.FeatureGroup();
        this._map.map.addLayer(drawnItems);

// Initialise the draw control and pass it the FeatureGroup of editable layers
        var drawControl = new L.Control.Draw({
            edit: {
                featureGroup: drawnItems
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

            $('#incident_polyline').val(layer.getLatLngs());


            // Do whatever else you need to. (save to db, add to map etc)
            that._map.map.addLayer(layer);
        });
    };

    IncidentEditPage.prototype._map = null;

    return IncidentEditPage;
});
