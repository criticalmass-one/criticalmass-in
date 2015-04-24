TrackContainer = function()
{
    
};

TrackContainer.prototype.list = new Array();
TrackContainer.prototype.layer = null;

TrackContainer.prototype.add = function(track)
{
    this.list.push(track);
};

TrackContainer.prototype.addTo = function(map)
{
    this.layer = L.featureGroup();
    
    for (index = 0; index < this.list.length; ++index) {
        this.list[index].addTo(this.layer);
    }

    this.layer.addTo(map);
};

TrackContainer.prototype.getBounds = function()
{
    return this.layer.getBounds();
};