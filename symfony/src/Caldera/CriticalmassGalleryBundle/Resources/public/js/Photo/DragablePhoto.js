DragablePhoto = function(id, latitude, longitude, title)
{
    this.id = id;
    this.latitude = latitude;
    this.longitude = longitude;
    this.title = title;
};

DragablePhoto.prototype = new Photo();

DragablePhoto.prototype.timestamp = null;

DragablePhoto.prototype.snapTo = function(map, polyline)
{
    this.marker.snapediting = new L.Handler.MarkerSnap(map, this.marker);
    this.marker.snapediting.addGuideLayer(polyline);
    this.marker.snapediting.enable();
};

DragablePhoto.prototype.addEvent = function(type, callback)
{
    this.marker.on(type, callback);
};

DragablePhoto.prototype.setTimestamp = function(timestamp)
{
    this.setTimestamp = timestamp;
};

DragablePhoto.prototype.addTo = function(map)
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

    this.marker = L.marker([this.latitude, this.longitude], { icon:locationIcon, draggable: true });
    this.marker.timestamp = this.timestamp;
    this.marker.addTo(map);

    var this2 = this;

    this.marker.on('click', function()
    {
        var photoPath = '/photos/' + this2.getId() + '.jpg';
        $.fancybox( { href : photoPath, title : this2.getTitle() } );
    });

    this.marker.on('dragend', function(event)
    {
        var marker = event.target;

        $.ajax({
            url: 'https://beta.criticalmass.cm/app_dev.php/gallery/photo/edit/relocate/' + this2.id,
            method: 'POST',
            data: {
                latitude: marker.getLatLng().lat,
                longitude: marker.getLatLng().lng
            }
        }).done(function(data)
        {
            if (console && console.log)
            {
                console.log("Sample of data:", data.slice(0, 100));
            }
        });
    });
};

