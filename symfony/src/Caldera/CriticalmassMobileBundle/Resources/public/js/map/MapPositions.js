MapPositions = function(map)
{
    this.map = map;
};

MapPositions.prototype.map = null;

MapPositions.prototype.positionsArray = [];

MapPositions.prototype.timer = null;

MapPositions.prototype.startLoop = function()
{
    this.drawPositions();

    var this2 = this;
    this.timer = window.setInterval(function()
    {
        this2.drawPositions();
    }, 2500);
};

MapPositions.prototype.clearOldPositions = function(ajaxResultData)
{
    for (existingUsername in this.positionsArray)
    {
        var found = false;

        for (index in ajaxResultData)
        {
            if (existingUsername == ajaxResultData[index].username)
            {
                found = true;
                break;
            }
        }

        if (!found)
        {
            this.removeUsernamePosition(existingUsername);
        }
    }
};

MapPositions.prototype.removeUsernamePosition = function(username)
{
    this.map.map.removeLayer(this.positionsArray[username]);
    delete this.positionsArray[username];
};

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

            if (this.positionsArray[position.username])
            {
                this.removeUsernamePosition(position.username);
            }

            this.positionsArray[position.username] = circle;
        }

        this.clearOldPositions(ajaxResultData);
    }

    $.support.cors = true;
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: UrlFactory.getNodeJSApiPrefix() + '?action=fetch&citySlug=' + this.map.parentPage.getCitySlug(),
        cache: false,
        context: this,
        crossDomain: true,
        success: callback
    });
};