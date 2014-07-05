DesktopMap = function(mapIdentifier)
{
    this.mapIdentifier = mapIdentifier;

    this.map = L.map(this.mapIdentifier);

    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(this.map);

    this.map.setView([51.505, -0.09], 13);
};

DesktopMap.prototype = new BaseMap();
DesktopMap.prototype.constructor = MobileMap;