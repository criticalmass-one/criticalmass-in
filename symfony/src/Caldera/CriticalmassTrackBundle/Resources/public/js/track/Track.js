Track = function()
{
    
    
};

Track.prototype.polyline = null;

Track.prototype.setPolyline = function(jsonData, colorRed, colorGreen, colorBlue)
{
    this.polyline = L.polyline(jsonData, { color: 'rgb(' + colorRed + ',' + colorGreen + ', ' + colorBlue + ')' });
};

Track.prototype.addTo = function(map)
{
    this.polyline.addTo(map);
};

Track.prototype.getBounds = function()
{
    return this.polyline.getBounds();
};