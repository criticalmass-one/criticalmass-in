define(['CriticalService', 'Map', 'leaflet-routing', 'leaflet-routing-draw', 'leaflet-routing-edit', 'leaflet-routing-storage', 'leaflet-snapping-lineutil', 'leaflet-snapping-marker', 'leaflet-snapping-polyline'], function(CriticalService) {
    TrackDrawPage = function(context, options) {
        this._CriticalService = CriticalService;

        this._initMap();
        this._initRouting();
        this._initEvents();
    };

    TrackDrawPage.prototype._map = null;
    TrackDrawPage.prototype._track = null;
    TrackDrawPage.prototype._routing = null;
    TrackDrawPage.prototype._CriticalService = null;

    TrackDrawPage.prototype._initMap = function() {
        this._map = new Map('map');
        this._map.setView([53, 9], 13);
    };

    TrackDrawPage.prototype._initRouting = function() {
        this._routing = new L.Routing({
            position: 'topright'
            ,routing: {
                router: this._router
            }
            ,tooltips: {
                waypoint: 'Waypoint. Drag to move; Click to remove.',
                segment: 'Drag to create a new waypoint'
            }
            ,styles: {     // see http://leafletjs.com/reference.html#polyline-options
                trailer: {}  // drawing line
                ,track: {}   // calculated route result
                ,nodata: {}  // line when no result (error)
            }
            ,snapping: {
                layers: []
                ,sensitivity: 15
                ,vertexonly: false
            }
            ,shortcut: {
                draw: {
                    enable: 68    // 'd'
                    ,disable: 81  // 'q'
                }
            }
        });

        this._map.map.addControl(this._routing);

        this._routing.draw();
    };

    TrackDrawPage.prototype._router = function(m1, m2, cb)
    {
        var proxy = '/routingproxy.php';
        var params = '?flat=' + m1.lat + '&flon=' + m1.lng + '&tlat=' + m2.lat + '&tlon=' + m2.lng;

        $.getJSON(proxy + params, function(geojson, status) {
            if (!geojson || !geojson.coordinates || geojson.coordinates.length === 0) {
                if (typeof console.log === 'function') {
                    console.log('OSM router failed', geojson);
                }
                return cb(new Error());
            }
            return cb(null, L.GeoJSON.geometryToLayer(geojson));
        });
    };

    TrackDrawPage.prototype._initEvents = function()
    {
        var that = this;

        $('#save-track').on('click', function() {
            alert(that._routing.toPolyline());
        });
    };


    return TrackDrawPage;
});