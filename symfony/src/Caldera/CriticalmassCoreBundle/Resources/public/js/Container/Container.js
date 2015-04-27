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

Container.prototype.countEntities = function()
{
    return this.list.length;
};

Container.prototype.addControl = function(layerArray, title)
{
    if (!this.isEmpty())
    {
        layerArray[title] = this.layer;
    }
};

Container.prototype.getBounds = function()
{
    return this.layer.getBounds();
};

Container.prototype.getEntity = function(index)
{
    return this.list[index];
};

Container.prototype.addLayer = function(index)
{
    this.list[index].addTo(this.layer);
};

Container.prototype.removeLayer = function(index)
{
    return this.list[index].removeLayer(this.layer);
};

Container.prototype.snapTo = function(map, polyline)
{
    for (var index in this.list)
    {
        this.list[index].snapTo(map, polyline);
    }
};

Container.prototype.addEvent = function(type, callback)
{
    for (var index in this.list)
    {
        this.list[index].addEvent(type, callback);
    }
};