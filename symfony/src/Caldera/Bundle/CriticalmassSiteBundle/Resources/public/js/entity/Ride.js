Ride = function(city, title, description, latitude, longitude, location, date, time, weather)
{
    this.city = city;
    this.title = title;
    this.description = description;
    this.latitude = latitude;
    this.longitude = longitude;
    this.location = location;
    this.date = date;
    this.time = time;
    this.weather = weather;
};

Ride.prototype.city = null;
Ride.prototype.title = null;
Ride.prototype.description = null;
Ride.prototype.latitude = null;
Ride.prototype.longitude = null;
Ride.prototype.location = null;
Ride.prototype.date = null;
Ride.prototype.time = null;
Ride.prototype.weather = null;
Ride.prototype.marker = null;

Ride.prototype.buildPopup = function()
{
    var html = '<h5>' + this.title + '</h5>';
    html += '<dl class="dl-horizontal">';
    html += '<dt>Datum:</dt><dd>' + this.date + '</dd>';
    html += '<dt>Uhrzeit:</dt><dd>' + this.time + '</dd>';
    html += '<dt>Treffpunkt:</dt><dd>' + this.location + '</dd>';
    html += '</dl>';
    html += '<p>' + this.description + '</p>';
    
    return html;
};

Ride.prototype.hasLocation = function()
{
    return (this.latitude != null && this.longitude != null && this.location != null && this.location != '');
};

Ride.prototype.addTo = function(markerLayer)
{
    if (this.hasLocation())
    {
        var locationIcon = L.icon({
            iconUrl: '/images/marker/marker-red.png',
            iconRetinaUrl: '/images/marker/marker-red-2x.png',
            iconSize: [25, 41],
            iconAnchor: [13, 41],
            popupAnchor: [0, -36],
            shadowUrl: '/images/marker/defaultshadow.png',
            shadowSize: [41, 41],
            shadowAnchor: [13, 41]
        });

        this.marker = L.marker([this.latitude, this.longitude], {icon: locationIcon});
        this.marker.bindPopup(this.buildPopup());

        markerLayer.addLayer(this.marker);
    }
    else
    {
        this.city.addTo(markerLayer, this);
    }
};

Ride.prototype.openPopup = function()
{
    if (this.hasLocation())
    {
        this.marker.openPopup();
    }
    else
    {
        this.city.openPopup();
    }
};