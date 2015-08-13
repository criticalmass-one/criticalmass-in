Live = function(citySlug, settings) {
    this.$$citySlug = citySlug;
    
    this.settings = $.extend(this.$$defaults, settings);
    
    this.$$init();
};

Live.prototype.$$defaults = {
    mapId: 'map',
    tileLayerUrl: 'https://api.tiles.mapbox.com/v4/maltehuebner.i1c90m12/{z}/{x}/{y}.png',
    mapBoxAccessToken: 'pk.eyJ1IjoibWFsdGVodWVibmVyIiwiYSI6IjB5c2QtNXcifQ.I7OHZr0wtAvqE0wIY_psfg',
    mapAttribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
};

Live.prototype.$$init = function() {
    this.$$initMap();
};

Live.prototype.$$initMap = function() {
    this.$$map = L.map(this.settings.mapId).setView([51.505, -0.09], 13);

    L.tileLayer(this.settings.tileLayerUrl + '?access_token=' + this.settings.mapBoxAccessToken, {
        attribution: this.settings.mapAttribution
    }).addTo(this.$$map);

    L.marker([51.5, -0.09]).addTo(this.$$map)
        .bindPopup('A pretty CSS3 popup.<br> Easily customizable.')
        .openPopup();
}

