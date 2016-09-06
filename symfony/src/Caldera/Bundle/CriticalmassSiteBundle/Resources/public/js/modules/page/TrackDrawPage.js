define(['CriticalService', 'RideEntity', 'CityEntity', 'Map', 'leaflet-polyline', 'leaflet-routing', 'leaflet-routing-draw', 'leaflet-routing-edit', 'leaflet-routing-storage', 'leaflet-snapping-lineutil', 'leaflet-snapping-marker', 'leaflet-snapping-polyline'], function(CriticalService) {
    TrackDrawPage = function(context, options) {
        this._CriticalService = CriticalService;
    };

    TrackDrawPage.prototype._map = null;
    TrackDrawPage.prototype._ride = null;
    TrackDrawPage.prototype._routing = null;
    TrackDrawPage.prototype._CriticalService = null;

    TrackDrawPage.prototype.init = function() {
        this._initMap();
        this._initRouting();
        this._initEvents();
    };

    TrackDrawPage.prototype.setRide = function(rideJson) {
        this._ride = this._CriticalService.factory.createRide(rideJson);
    };

    TrackDrawPage.prototype._initMap = function() {
        this._map = new Map('map');

        this._map.setView([this._ride.getLatitude(), this._ride.getLongitude()], 13);
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

    TrackDrawPage.prototype._router = function(m1, m2, cb) {
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

    TrackDrawPage.prototype._initEvents = function() {
        var that = this;

        $('#save-track').on('click', function(element) {
            element.preventDefault();
            that._save();
        });
    };

    TrackDrawPage.prototype._save = function() {
        this._savePolyline();
        this._saveWaypoints();

        $('form').submit();
    };

    TrackDrawPage.prototype._savePolyline = function() {
        var polyline = this._routing.toPolyline();
        var latLngs = polyline.getLatLngs();

        var polylineString = L.PolylineUtil.encode(latLngs);

        $('#polyline').val(polylineString);
    };

    TrackDrawPage.prototype._saveWaypoints = function() {
        var geoJsonString = JSON.stringify(this._routing.toGeoJSON());

        $('#geojson').val(geoJsonString);
    };


    return TrackDrawPage;
});