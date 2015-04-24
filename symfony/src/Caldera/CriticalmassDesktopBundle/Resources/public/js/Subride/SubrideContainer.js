SubrideContainer = function()
{
    
};

SubrideContainer.prototype.list = new Array();
SubrideContainer.prototype.layer = null;

SubrideContainer.prototype.add = function(subride)
{
    this.list.push(subride);
};

SubrideContainer.prototype.addTo = function(map)
{
    this.layer = L.featureGroup();
    
    for (index = 0; index < this.list.length; ++index) {
        this.list[index].addTo(this.layer);
    }

    this.layer.addTo(map);
};