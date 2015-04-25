Container = function()
{

};

Container.prototype.list = new Array();
Container.prototype.layer = null;

Container.prototype.add = function(entity)
{
    this.list.push(entity);
};

Container.prototype.addTo = function(map)
{
    this.layer = L.featureGroup();

    for (index = 0; index < this.list.length; ++index) {
        this.list[index].addTo(this.layer);
    }

    this.layer.addTo(map);
};