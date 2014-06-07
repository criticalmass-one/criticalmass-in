MapPositions = function(map)
{
    this.map = map;
};

MapPositions.prototype.map = null;

MapPositions.prototype.positionsArray = [];

MapPositions.prototype.timer = null;

MapPositions.prototype.autoFollowUsername = null;

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

MapPositions.prototype.createUsernamePosition = function(position)
{
    var this2 = this;
    var userColor = 'rgb(' + position.colorRed + ', ' + position.colorGreen + ', ' + position.colorBlue + ')';

    var circleOptions = {
        color: userColor,
        fillColor: userColor,
        opacity: 0.8,
        fillOpacity: 0.5,
        weight: 1,
        username: position.username,
        timestamp: position.timestamp
    };

    var circle = L.circle([position.latitude, position.longitude], 25, circleOptions);
    circle.addTo(this.map.map);
    circle.bindPopup(position.username);

    if (position.username == this2.autoFollowUsername && this2.map.parentPage.isAutoFollow())
    {
        this2.map.setViewLatLngZoom(position.latitude, position.longitude, 15);
    }

    circle.on('click', function(element) {
        var latLng = element.target.getLatLng();
        this2.map.setViewLatLngZoom(latLng.lat, latLng.lng, 15);
        this2.autoFollowUsername = element.target.options.username;
        this2.map.parentPage.setAutoFollow(true);
    }.bind(this2));

    this.positionsArray[position.username] = circle;
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
            }
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

MapPositions.prototype.panToLatestPosition = function()
{
    var maxTimestamp = 0;
    var maxTimestampIndex = 0;

    for (index in this.positionsArray)
    {
        if (this.positionsArray[index].options.timestamp > maxTimestamp)
        {
            maxTimestamp = this.positionsArray[index].options.timestamp;
            maxTimestampIndex = index;
        }
    }

    this.map.map.panTo(this.positionsArray[maxTimestampIndex].getLatLng());
};