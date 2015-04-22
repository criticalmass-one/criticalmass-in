RideContainer = function()
{
    
};

RideContainer.prototype.list = new Array();
RideContainer.prototype.markerGroup = null;

RideContainer.prototype.add = function(ride)
{
    this.list.push(ride);
};

RideContainer.prototype.addTo = function(map)
{
    this.markerGroup = L.featureGroup();
    
    for (index = 0; index < this.list.length; ++index) {
        this.list[index].addTo(this.markerGroup);
    }

    this.markerGroup.addTo(map);
};