MapPositions = function(map)
{
    this.map = map;
};

MapPositions.prototype.map = null;

MapPositions.prototype.positionsArray = [];

MapPositions.prototype.drawPositions = function()
{
    function callback(ajaxResultData)
    {
        for (index in ajaxResultData)
        {
            var position = ajaxResultData[index];

            var userColor = 'rgb(' + position.colorRed + ', ' + position.colorGreen + ', ' + position.colorBlue + ')';

            var circleOptions = {
                color: userColor,
                fillColor: userColor,
                opacity: 80,
                fillOpacity: 50,
                weight: 1
            };

            var circle = L.circle([position.latitude, position.longitude], 25, circleOptions);
            circle.addTo(this.map.map);
            circle.bindPopup(position.username);

            this.positionsArray[position.username] = circle;
        }
    }

    $.support.cors = true;
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: UrlFactory.getNodeJSApiPrefix() + '?action=fetch&rideId=1',
        cache: false,
        context: this,
        crossDomain: true,
        success: callback
    });
};