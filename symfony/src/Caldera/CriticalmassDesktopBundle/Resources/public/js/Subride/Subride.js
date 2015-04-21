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
    
    var subRideMarker = L.marker([this.latitude, this.longitude], { icon: subRideIcon });
    subRideMarker.addTo(map);
    subRideMarker.bindPopup('<h5>' + this.title + '</h5><dl class="dl-horizontal"><dt>Uhrzeit:</dt><dd>' + this.datetime + '</dd><dt>Treffpunkt:</dt><dd>' + this.location + '</dd></dl><p>' + this.description + '</p>');
//subRideMarkers[{{ subRide.getId() }}] = subRideMarker;
    markerLayer.addLayer(subRideMarker);


    
}
