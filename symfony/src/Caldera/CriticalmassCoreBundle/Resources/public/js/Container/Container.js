Container = function()
{
    this.list = [];
    this.layer = L.featureGroup();
};

Container.prototype.list = null;
Container.prototype.layer = null;

Container.prototype.add = function(entity)
{
    this.list.push(entity);
};

Container.prototype.addTo = function(map)
{
    for (index = 0; index < this.list.length; ++index) {
        this.list[index].addTo(this.layer);
    }

    this.layer.addTo(map);
};

Container.prototype.isEmpty = function()
{
    return this.list.length == 0;
};