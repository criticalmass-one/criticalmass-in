Gallery = function()
{
};

Gallery.prototype.init = function(map, photoArray)
{
    var photo;

    while (photo = photoArray.pop())
    {
        photo.addTo(map);
    }
};