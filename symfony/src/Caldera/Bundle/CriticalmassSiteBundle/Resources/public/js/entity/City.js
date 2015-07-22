City = function(title, name, slug, description, latitude, longitude)
{
    this.title = title;
    this.name = name;
    this.slug = slug;
    this.description = description;
    this.latitude = latitude;
    this.longitude = longitude;
};

City.prototype.title = null;
City.prototype.name = null;
City.prototype.slug = null;
City.prototype.description = null;
City.prototype.latitude = null;
City.prototype.longitude = null;
City.prototype.marker = null;

City.prototype.buildPopup = function(ride)
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

City.prototype.addTo = function(markerLayer, ride)
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

City.prototype.openPopup = function()
{
    this.marker.openPopup();
};