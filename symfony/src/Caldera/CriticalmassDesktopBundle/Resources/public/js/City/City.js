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

City.prototype.addTo = function(markerLayer)
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
    
    var cityMarker = L.marker([this.latitude, this.longitude], { icon: cityIcon });
    cityMarker.bindPopup('<h5>' + this.title + '</h5><dl class="dl-horizontal"><dt>Uhrzeit:</dt><dd>' + this.datetime + '</dd><dt>Treffpunkt:</dt><dd>' + this.location + '</dd></dl><p>' + this.description + '</p>');
    markerLayer.addLayer(cityMarker);
};
