CityContainer = function()
{
    
};

CityContainer.prototype.list = new Array();
CityContainer.prototype.markerGroup = null;

CityContainer.prototype.add = function(city)
{
    this.list.push(city);
};

CityContainer.prototype.addTo = function(map)
{
    this.markerGroup = L.featureGroup();
    
    for (index = 0; index < this.list.length; ++index) {
        this.list[index].addTo(this.markerGroup);
    }

    this.markerGroup.addTo(map);
};