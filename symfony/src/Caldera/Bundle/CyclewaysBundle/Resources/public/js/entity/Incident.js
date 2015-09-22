Incident = function(caption, description, datetime, latitude, longitude)
{
    this.caption = caption;
    this.description = description;
    this.datetime = datetime;
    this.latitude = latitude;
    this.longitude = longitude;
};

Incident.prototype.caption = null;
Incident.prototype.description = null;
Incident.prototype.datetime = null;
Incident.prototype.latitude = null;
Incident.prototype.longitude = null;
Incident.prototype.marker = null;

Incident.prototype.buildPopup = function(ride)
{
    var html = '<h5>' + this.title + '</h5>';
    
    if (ride != null)
    {
        html += '<dl class="dl-horizontal">';
        html += '<dt>Uhrzeit:</dt><dd>' + ride.date + ' ' + ride.time + ' Uhr</dd>';
        html += '<dt>Treffpunkt:</dt><dd><em>noch nicht bekannt</em></dd>';
        html += '</dl>';
    }
    else
    {
        html += '<p><em>Zu dieser Stadt sind momentan leider keine Tourinformationen bekannt.</em>';
    }
    
    html += '<p>' + this.description + '</p>';
    
    return html;
};

Incident.prototype.addTo = function(markerLayer, ride)
{
    var cityIcon = L.icon({
        iconUrl: '/images/marker/marker-gray.png',
        iconRetinaUrl: '/images/marker/marker-gray-2x.png',
        iconSize: [25, 41],
        iconAnchor: [13, 41],
        popupAnchor: [0, -36],
        shadowUrl: '/images/marker/defaultshadow.png',
        shadowSize: [41, 41],
        shadowAnchor: [13, 41]
    });

    this.marker = L.marker([this.latitude, this.longitude], {icon: cityIcon});
    this.marker.bindPopup(this.buildPopup(ride));
    markerLayer.addLayer(this.marker);
};

Incident.prototype.openPopup = function()
{
    this.marker.openPopup();
};