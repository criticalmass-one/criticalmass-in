TrackContainer = function()
{
    
};

TrackContainer.prototype.list = new Array();
TrackContainer.prototype.trackGroup = null;

TrackContainer.prototype.add = function(track)
{
    this.list.push(track);
};

TrackContainer.prototype.addTo = function(map)
{
    this.trackGroup = L.featureGroup();
    
    for (index = 0; index < this.list.length; ++index) {
        this.list[index].addTo(this.trackGroup);
    }

    this.trackGroup.addTo(map);
};