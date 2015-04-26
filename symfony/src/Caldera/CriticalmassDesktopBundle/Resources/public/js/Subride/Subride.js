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

Subride.prototype.buildPopup = function()
{
    var html = '<h5>' + this.title + '</h5>';
    html += '<dl class="dl-horizontal">';
    html += '<dt>Uhrzeit:</dt><dd>' + this.datetime + '</dd>';
    html += '<dt>Treffpunkt:</dt><dd>' + this.location + '</dd>';
    html += '</dl>';
    html += '<p>' + this.description + '</p>';
    
    return html;
};

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
    this.marker.bindPopup(this.buildPopup());
    markerLayer.addLayer(this.marker);
};

Subride.prototype.openPopup = function()
{
    this.marker.openPopup();
};

Subride.prototype.getLatLng = function()
{
    return [this.latitude, this.longitude];
};