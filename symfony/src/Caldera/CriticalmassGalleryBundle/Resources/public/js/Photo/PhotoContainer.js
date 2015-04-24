PhotoContainer = function()
{
};

PhotoContainer.prototype.photoArray = new Array();

PhotoContainer.prototype.addPhotos = function(photoArray)
{
    this.photoArray = $.merge(this.photoArray, photoArray);
};

PhotoContainer.prototype.addPhoto = function(photo)
{
    this.photoArray[photo.getId()] = photo;
};

PhotoContainer.prototype.map = null;

PhotoContainer.prototype.addTo = function(map)
{
    this.map = map;
    
    this.init();
};

PhotoContainer.prototype.init = function()
{
    var photo;

    while (photo = this.photoArray.pop())
    {
        photo.addTo(this.map);
    }
};