Map = function(mapId, settings) {
    this.$$mapId = mapId;
    
    this.settings = $.extend(this.$$defaults, settings);

    this.$$init();
};

Map.prototype.$$defaults = {
    tileLayerUrl: 'https://api.tiles.mapbox.com/v4/maltehuebner.j385n2ak/{z}/{x}/{y}.png',
    mapBoxAccessToken: 'pk.eyJ1IjoibWFsdGVodWVibmVyIiwiYSI6IjB5c2QtNXcifQ.I7OHZr0wtAvqE0wIY_psfg',
    mapAttribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
    detectRetina: true,
    defaultLatitude: 37.680349,
    defaultLongitude: -1.335927,
    defaultZoom: 5,
    showZoomControl: true,
    zoomControlPosition: 'bottomright'
};

Map.prototype.$$init = function() {
    this.$$initMap();
    this.$$addTileLayer();
};

Map.prototype.$$initMap = function() {
    var defaultLatLng = L.latLng(this.settings.defaultLatitude, this.settings.defaultLongitude);
    
    this.map = L.map(this.$$mapId, { zoomControl: false });
    
    if (this.settings.showZoomControl) {
        this.addZoomControl(this.settings.zoomControlPosition);
    }
    
    this.map.setView(defaultLatLng, this.settings.defaultZoom);
};

Map.prototype.addZoomControl = function(zoomControlPosition) {
    var zoomControl = new L.Control.Zoom({ position: zoomControlPosition });
    
    zoomControl.addTo(this.map);
};

Map.prototype.$$addTileLayer = function() {
    L.tileLayer(this.settings.tileLayerUrl + '?access_token=' + this.settings.mapBoxAccessToken, {
        attribution: this.settings.mapAttribution,
        detectRetina: this.settings.detectRetina
    }).addTo(this.map);
};

Map.prototype.setView = function(latLng, zoom) {
    //this.map.setView(latLng, zoom);
};

Marker = function(latLng, draggable) {
    this.$$latLng = latLng;
    this.$$draggable = draggable;
};

Marker.prototype.$$latLng = null;
Marker.prototype.$$draggable = false;
Marker.prototype.$$icon = null;

Marker.prototype.addTo = function(map) {
    this.$$marker = L.marker(this.$$latLng, {
                        icon: this.$$icon,
                        draggable: this.$$draggable
    });

    this.$$marker.addTo(map.map);
};

Marker.prototype.removeFrom = function(map) {
    map.map.removeLayer(this.$$marker);
};

Marker.prototype.addPopupText = function(popupText, openPopup) {
    if (openPopup) {
        this.$$marker.bindPopup(popupText).openPopup();
    } else {
        this.$$marker.bindPopup(popupText);
    }
};

Marker.prototype.on = function(event, func) {
    this.$$marker.on(event, func);
};

CityMarker = function(latLng, draggable) {
    this.$$latLng = latLng;
    this.$$draggable = draggable;

    this.$$icon = L.icon({
        iconUrl: '/images/marker/marker-red.png',
        iconRetinaUrl: '/images/marker/marker-red-2x.png',
        iconSize: [25, 41],
        iconAnchor: [13, 41],
        popupAnchor: [0, -36],
        shadowUrl: '/images/marker/defaultshadow.png',
        shadowSize: [41, 41],
        shadowAnchor: [13, 41]
    });
};

CityMarker.prototype = new Marker();
CityMarker.prototype.constructor = CityMarker;

LocationMarker = function(latLng, draggable) {
    this.$$latLng = latLng;
    this.$$draggable = draggable;

    this.$$icon = L.icon({
        iconUrl: '/images/marker/marker-yellow.png',
        iconRetinaUrl: '/images/marker/marker-yellow-2x.png',
        iconSize: [25, 41],
        iconAnchor: [13, 41],
        popupAnchor: [0, -36],
        shadowUrl: '/images/marker/defaultshadow.png',
        shadowSize: [41, 41],
        shadowAnchor: [13, 41]
    });
};

LocationMarker.prototype = new Marker();
LocationMarker.prototype.constructor = LocationMarker;