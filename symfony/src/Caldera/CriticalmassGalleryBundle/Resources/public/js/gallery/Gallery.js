Gallery = function()
{
};

Gallery.prototype.photoArray = new Array();

Gallery.prototype.addPhotos = function(photoArray)
{
    this.photoArray.merge(photoArray);
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

    while (photo = photoArray.pop())
    {
        photo.addTo(this.map);
    }
};