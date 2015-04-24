CityContainer = function()
{
    
};

CityContainer.prototype.list = new Array();
CityContainer.prototype.layer = null;

CityContainer.prototype.add = function(city)
{
    this.list.push(city);
};

CityContainer.prototype.addTo = function(map)
{
    this.layer = L.layer();
    
    for (index = 0; index < this.list.length; ++index) {
        this.list[index].addTo(this.layer);
    }

    this.layer.addTo(map);
};