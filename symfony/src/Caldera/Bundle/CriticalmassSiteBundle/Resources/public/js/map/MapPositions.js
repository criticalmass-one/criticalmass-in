MapPositions = function()
{

};

MapPositions.prototype.map = null;

MapPositions.prototype.layerGroup = null;

MapPositions.prototype.positionsArray = new Array();

MapPositions.prototype.positionsCounter = 0;

MapPositions.prototype.timer = null;

MapPositions.prototype.addTo = function(map) 
{
    this.map = map;

    this.layerGroup = L.layerGroup();
    this.layerGroup.addTo(this.map.map);
};

MapPositions.prototype.start = function()
{
    this.drawPositions();

    var this2 = this;
    this.timer = window.setInterval(function()
    {
        this2.drawPositions();

    }, 5000);
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
    this.layerGroup.removeLayer(this.positionsArray[username]);
    delete this.positionsArray[username];
    --this.positionsCounter;
};

MapPositions.prototype.createUsernamePosition = function(position)
{
    var this2 = this;
    var userColor = 'rgb(' + position.colorRed + ', ' + position.colorGreen + ', ' + position.colorBlue + ')';

    var circleOptions = {
        color: userColor,
        fillColor: userColor,
        opacity: 1,
        fillOpacity: 0.75,
        weight: 3,
        username: position.username,
        timestamp: position.timestamp,
        citySlug: position.citySlug
    };

    var circle = L.circle([position.latitude, position.longitude], 35, circleOptions);
    circle.addTo(this.layerGroup);
    circle.bindPopup(position.username);

    this.positionsArray[position.username] = circle;
    ++this.positionsCounter;
};

MapPositions.prototype.moveUsernamePosition = function(username, latitude, longitude)
{
    this.positionsArray[username].setLatLng([latitude, longitude]);
};

MapPositions.prototype.drawPositions = function()
{
    function callback(ajaxResultData)
    {
        for (index in ajaxResultData)
        {
            if (!this.positionsArray[ajaxResultData[index].username])
            {
                this.createUsernamePosition(ajaxResultData[index]);
            }
            else
            {
                this.moveUsernamePosition(ajaxResultData[index].username, ajaxResultData[index].latitude, ajaxResultData[index].longitude);

                if (!this.isUserPositionColor(ajaxResultData[index].username, ajaxResultData[index].colorRed, ajaxResultData[index].colorGreen, ajaxResultData[index].colorBlue))
                {
                    this.setUserPositionColor(ajaxResultData[index].username, ajaxResultData[index].colorRed, ajaxResultData[index].colorGreen, ajaxResultData[index].colorBlue);
                }
            }
        }

        this.clearOldPositions(ajaxResultData);

        CallbackHell.executeEventListener('mapPositionsMarkersDrawn');
    }

    $.support.cors = true;
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: Url.getNodeJSApiPrefix() + '?action=fetchPositions&citySlug=all',
        cache: false,
        context: this,
        crossDomain: true,
        success: callback
    });
};

MapPositions.prototype.isUserPositionColor = function(username, colorRed, colorGreen, colorBlue)
{
    return this.positionsArray[username].options.color == 'rgb(' + colorRed + ', ' + colorGreen + ', ' + colorBlue + ')';
};

MapPositions.prototype.setUserPositionColor = function(username, colorRed, colorGreen, colorBlue)
{
    var userColor = 'rgb(' + colorRed + ', ' + colorGreen + ', ' + colorBlue + ')';
    var circleOptions = {
        color: userColor,
        fillColor: userColor
    };

    this.positionsArray[username].setStyle(circleOptions);
};

MapPositions.prototype.getLatestPosition = function(requestedSlug)
{
    var maxTimestamp = 0;
    var maxTimestampIndex = 0;

    for (index in this.positionsArray)
    {
        var position = this.positionsArray[index];

        if (position.options.citySlug == requestedSlug && position.options.timestamp > maxTimestamp)
        {
            maxTimestamp = this.positionsArray[index].options.timestamp;
            maxTimestampIndex = index;
        }
    }

    return this.positionsArray[maxTimestampIndex];
};

MapPositions.prototype.panToLatestPosition = function(requestedSlug)
{
    var latestPosition = this.getLatestPosition(requestedSlug);

    this.map.panTo(latestPosition.getLatLng());
};