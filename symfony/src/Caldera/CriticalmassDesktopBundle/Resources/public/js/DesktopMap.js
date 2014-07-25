DesktopMap = function(mapIdentifier)
{
    this.mapIdentifier = mapIdentifier;

    this.map = L.map(this.mapIdentifier);

    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(this.map);

    this.map.setView([53.5511383, 9.9949955], 13);

    this.cities = new MapCities(this);

    var this2 = this;
    CallbackHell.registerEventListener('cityListRefreshed', function()
    {
        this2.cities.drawCityMarkers();
    });
};

DesktopMap.prototype = new BaseMap();
DesktopMap.prototype.constructor = DesktopMap;