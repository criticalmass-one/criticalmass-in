MapPositionTrace = function(map)
{
    this.map = map;
};

MapPositionTrace.prototype.map = null;

MapPositionTrace.prototype.mapPositions = null;

MapPositionTrace.prototype.positionsArray = new Array();

MapPositionTrace.prototype.addPosition = function(position)
{
    this.positionsArray[position.username].push(position);

};

MapPositionTrace.prototype.drawPositionTrace = function()
{
    for (var username in this.positionArray)
    {
        var latLngs = new Array();

        for (var index in this.positionArray[username])
        {
            var position = this.positionArray[username][index];

            var latLng = L.latLng(position.latitude, position.longitude);

            latLngs.push(latLng);
        }

        options = {
            color: "#f00"
        };

        L.multiPolyline(latlngs, options);
    }
};