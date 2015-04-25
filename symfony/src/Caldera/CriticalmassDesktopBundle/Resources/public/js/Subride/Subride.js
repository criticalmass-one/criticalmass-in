Subride = function(title, description, datetime, latitude, longitude, location)
{
    this.title = title;
    this.description = description;
    this.datetime = datetime;
    this.latitude = latitude;
    this.longitude = longitude;
    this.location = location;
};

Subride.prototype.title = null;
Subride.prototype.description = null;
Subride.prototype.latitude = null;
Subride.prototype.longitude = null;
Subride.prototype.datetime = null;
Subride.prototype.location = null;
Subride.prototype.marker = null;

Subride.prototype.addTo = function(markerLayer)
{
    var subRideIcon = L.icon({
        iconUrl: '/images/marker/marker-blue.png',
        iconRetinaUrl: '/images/marker/marker-blue-2x.png',
        iconSize: [25, 41],
        iconAnchor: [13, 41],
        popupAnchor: [0, -36],
        shadowUrl: '/images/marker/defaultshadow.png',
        shadowSize: [41, 41],
        shadowAnchor: [13, 41]
    });
    
    this.marker = L.marker([this.latitude, this.longitude], { icon: subRideIcon });
    this.marker.addTo(map);
    this.marker.bindPopup('<h5>' + this.title + '</h5><dl class="dl-horizontal"><dt>Uhrzeit:</dt><dd>' + this.datetime + '</dd><dt>Treffpunkt:</dt><dd>' + this.location + '</dd></dl><p>' + this.description + '</p>');
    markerLayer.addLayer(this.marker);
};

Subride.prototype.openPopup = function()
{
    this.marker.openPopup();
};