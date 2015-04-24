RideContainer = function()
{
    
};

RideContainer.prototype.list = new Array();
RideContainer.prototype.layer = null;

RideContainer.prototype.add = function(ride)
{
    this.list.push(ride);
};

RideContainer.prototype.addTo = function(map)
{
    this.layer = L.featureGroup();
    
    for (index = 0; index < this.list.length; ++index) {
        this.list[index].addTo(this.layer);
    }

    this.layer.addTo(map);
};