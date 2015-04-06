Photo = function(id, latitude, longitude, title)
{
    this.id = id;
    this.latitude = latitude;
    this.longitude = longitude;
    this.title = title;
};

Photo.prototype.id = null;
Photo.prototype.latitude = null;
Photo.prototype.longitude = null;
Photo.prototype.title = null;

Photo.prototype.getId = function()
{
    return this.id;
};

Photo.prototype.getLatitude = function()
{
    return this.latitude;
};

Photo.prototype.getLongitude = function()
{
    return this.longitude;
};

Photo.prototype.getTitle = function()
{
    return this.title;
};

Photo.prototype.addTo = function(map)
{
    var locationIcon = L.icon({
        iconUrl: 'https://www.criticalmass.in/images/marker/marker-red.png',
        iconRetinaUrl: '/images/marker/marker-red-2x.png',
        iconSize: [25, 41],
        iconAnchor: [13, 41],
        popupAnchor: [0, -36],
        shadowUrl: '/images/marker/defaultshadow.png',
        shadowSize: [41, 41],
        shadowAnchor: [13, 41]
    });

    var photoMarker = L.marker([this.getLatitude(), this.getLongitude()], { icon:locationIcon, draggable: false });
    photoMarker.addTo(map);

    var this2 = this;

    photoMarker.on('click', function()
    {
        var photoPath = '/photos/' + this2.getId() + '.jpg';
        $.fancybox( { href : photoPath, title : this2.getTitle() } );
    });
};