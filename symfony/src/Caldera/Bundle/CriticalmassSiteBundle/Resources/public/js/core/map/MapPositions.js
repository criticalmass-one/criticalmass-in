MapPositions = function(map)
{
    this.map = map;
};

MapPositions.prototype.map = null;

MapPositions.prototype.positionsArray = new Array();

/**
 * Assoziative Arrays in JavaScript funktionieren nicht wie ein Array, sondern
 * wie ein Objekt mit zusätzlichen Eigenschaften. Die Länge eines assoziativen
 * Arrays ist darum immer null, also muss die tatsächliche Anzahl der Position-
 * en bedauerlicherweise in einer zweiten Eigenschaft gespeichert werden.
 * @type {number}
 */
MapPositions.prototype.positionsCounter = 0;

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
        timestamp: position.timestamp
    };

    var circle = L.circle([position.latitude, position.longitude], 35, circleOptions);
    circle.addTo(this.map.map);
    circle.bindPopup(position.username);

    circle.on('click', function(element) {
        var latLng = element.target.getLatLng();
        this2.map.setViewLatLngZoom(latLng.lat, latLng.lng, 15);
        this2.autoFollowUsername = element.target.options.username;
        _paq.push(['trackEvent', 'autoFollow', 'start']);
        this2.map.parentPage.setAutoFollow(true);
    }.bind(this2));

    this.positionsArray[position.username] = circle;
    ++this.positionsCounter;
};

MapPositions.prototype.moveUsernamePosition = function(username, latitude, longitude)
{
    this.positionsArray[username].setLatLng([latitude, longitude]);

    if (username == this.autoFollowUsername /*&& this2.map.parentPage.isAutoFollow()*/)
    {
        this.map.map.panTo([latitude, longitude]);
    }
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
        this.setQuickLinkButtonStatus(this.positionsCounter > 0);

        CallbackHell.executeEventListener('mapPositionsMarkersDrawn');
    }

    $.support.cors = true;
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: Url.getNodeJSApiPrefix() + '?action=fetchPositions&citySlug=' + this.map.parentPage.getCitySlug(),
        cache: false,
        context: this,
        crossDomain: true,
        success: callback
    });
};

MapPositions.prototype.getLatestPosition = function()
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

    return this.positionsArray[maxTimestampIndex];
};

MapPositions.prototype.panToLatestPosition = function()
{
    var latestPosition = this.getLatestPosition();

    this.map.map.panTo(latestPosition.getLatLng());

    this.autoFollowUsername = latestPosition.options.username;

    _paq.push(['trackEvent', 'panTo', 'latestPosition']);
};

MapPositions.prototype.stopAutoFollowing = function()
{
    this.autoFollowUsername = null;

    _paq.push(['trackEvent', 'autoFollow', 'start']);
};

MapPositions.prototype.setQuickLinkButtonStatus = function(status)
{
    $('#quicklinkLatestPosition').attr('disabled', !status);
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