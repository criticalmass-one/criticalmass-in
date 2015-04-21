SubrideContainer = function()
{
    
    
};

SubrideContainer.prototype.list = new Array();
SubrideContainer.prototype.markerGroup = null;

SubrideContainer.prototype.add = function(subride)
{
    this.list.push(subride);
};

SubrideContainer.prototype.addTo = function(map)
{
    this.markerGroup = L.featureGroup();
    
    for (index = 0; index < this.list.length; ++index) {
        this.list[index].addTo(markerGroup);
    }

    this.markerGroup.addTo(map);
};