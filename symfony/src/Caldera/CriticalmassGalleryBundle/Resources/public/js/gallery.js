Gallery = function()
{
};

Gallery.prototype.init = function(photoArray)
{
    var photo;
    
    while (photo = photoArray.pop())
    {
        alert(photo.getId());
    }
};

Gallery.prototype.test = function()
{
    //alert('foo');
};

Photo = function(id, latitude, longitude)
{
    this.id = id;
    this.latitude = latitude;
    this.longitude = longitude;
}

Photo.prototype.address = null;
Photo.prototype.latitude = null;
Photo.prototype.longitude = null;

Photo.prototype.getId = function()
{
    return this.id;
}

Photo.prototype.getLatitude = function()
{
    return this.latitude;
}

Photo.prototype.getLongitude = function()
{
    return this.longitude;
}