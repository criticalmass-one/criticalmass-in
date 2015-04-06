Gallery = function()
{
};

Gallery.prototype.photoArray = new Array();

Gallery.prototype.addPhotos = function(photoArray)
{
    this.photoArray = $.merge(this.photoArray, photoArray);
};

Gallery.prototype.addPhoto = function(photo)
{
    this.photoArray[photo.getId()] = photo;
};

Gallery.prototype.map = null;

Gallery.prototype.addTo = function(map)
{
    this.map = map;
    
    this.init();
};

Gallery.prototype.init = function()
{
    var photo;

    while (photo = this.photoArray.pop())
    {
        photo.addTo(this.map);
    }
};