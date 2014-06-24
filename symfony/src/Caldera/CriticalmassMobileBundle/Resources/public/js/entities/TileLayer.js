TileLayer = function()
{
};

TileLayer.prototype.id = null;
TileLayer.prototype.title = null;
TileLayer.prototype.address = null;
TileLayer.prototype.attributation = null;

TileLayer.prototype.getId = function()
{
    return this.id;
};

TileLayer.prototype.setId = function(id)
{
    this.id = id;
};

TileLayer.prototype.getTitle = function()
{
    return this.title;
};

TileLayer.prototype.setTitle = function(title)
{
    this.title = title;
};

TileLayer.prototype.getAddress = function()
{
    return this.address;
};

TileLayer.prototype.setAddress = function(address)
{
    this.address = address;
};

TileLayer.prototype.getAttributation = function()
{
    return this.attributation;
};

TileLayer.prototype.setAttributation = function(attributation)
{
    this.attributation = attributation;
};

TileLayer.prototype.getStandard = function()
{
    return this.standard;
};

TileLayer.prototype.setStandard = function(standard)
{
    this.standard = standard;
};

TileLayer.prototype.isAvailable = function()
{
    return this.address.length > 0;
};