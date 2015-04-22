Ride = function(title, description, latitude, longitude, location, date, time, weather)
{
    this.title = title;
    this.description = description;
    this.latitude = latitude;
    this.longitude = longitude;
    this.location = location;
    this.date = date;
    this.time = time;
    this.weather = weather;
};

Ride.prototype.title = null;
Ride.prototype.description = null;
Ride.prototype.latitude = null;
Ride.prototype.longitude = null;
Ride.prototype.location = null;
Ride.prototype.date = null;
Ride.prototype.time = null;
Ride.prototype.weather = null;

Ride.prototype.addTo = function(markerLayer)
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
    
    var rideMarker = L.marker([this.latitude, this.longitude], { icon: locationIcon });
    rideMarker.addTo(map);
    rideMarker.bindPopup('<h5>' + this.title + '</h5><dl class="dl-horizontal"><dt>Uhrzeit:</dt><dd>' + this.datetime + '</dd><dt>Treffpunkt:</dt><dd>' + this.location + '</dd></dl><p>' + this.description + '</p>');
    markerLayer.addLayer(rideMarker);
};
