PhotoContainer = function()
{

};

PhotoContainer.prototype.list = new Array();
PhotoContainer.prototype.layer = null;

PhotoContainer.prototype.add = function(photo)
{
    this.list.push(photo);
};

PhotoContainer.prototype.addTo = function(map)
{
    this.layer = L.featureGroup();

    for (index = 0; index < this.list.length; ++index) {
        this.list[index].addTo(this.layer);
    }

    this.layer.addTo(map);
};