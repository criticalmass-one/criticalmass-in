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
    this.timer = window.setInterval(function() {
        this2.drawPositions();

    }, 5000);
};

MapPositions.prototype.clearOldPositions = function(resultArray)
{
    for (existingDisplayName in this.positionsArray) {
        var found = false;

        for (index in resultArray) {
            if (existingDisplayName == resultArray[index].displayName) {
                found = true;
                break;
            }
        }

        if (!found) {
            this.removeUsernamePosition(existingDisplayName);
        }
    }
};

MapPositions.prototype.removeUsernamePosition = function(displayName)
{
    this.layerGroup.removeLayer(this.positionsArray[displayName]);
    delete this.positionsArray[displayName];
    --this.positionsCounter;
};

MapPositions.prototype.createUsernamePosition = function(position)
{
    var userColor = 'rgb(' + position.displayColor.red + ', ' + position.displayColor.green + ', ' + position.displayColor.blue + ')';

    var circleOptions = {
        color: userColor,
        fillColor: userColor,
        opacity: 1,
        fillOpacity: 0.75,
        weight: 3,
        username: position.displayName,
        timestamp: position.timestamp,
        citySlug: position.citySlug
    };

    var circle = L.circle([position.coord.latitude, position.coord.longitude], 35, circleOptions);
    //circle.addTo(this.layerGroup);
    circle.addTo(this.map.map);
    circle.bindPopup(position.displayName);

    this.positionsArray[position.displayName] = circle;
    ++this.positionsCounter;
    //console.log('erstelle ' + position.displayName);
    console.log('Jetzt ' + this.positionsCounter + ' Positionen gespeichert');
};

MapPositions.prototype.moveUsernamePosition = function(displayName, coord)
{
    this.positionsArray[displayName].setLatLng([coord.latitude, coord.longitude]);
};

MapPositions.prototype.drawPositions = function()
{
    /* yeah: normally this should work this the context option of the jquery stuff, but somehow it does not, so we use
    this nasty workaround here. */
    var that = this;
    
    function callback(ajaxResultData)
    {
        console.log('Bislang ' + that.positionsCounter + ' Positionen gespeichert');
        var resultArray = ajaxResultData.result;
        
        for (index in resultArray) {
            if (!that.positionsArray[resultArray[index].displayName]) {
                that.createUsernamePosition(resultArray[index]);
                console.log(resultArray[index].displayName + ' existiert noch nicht');
            } else {
                console.log(resultArray[index].displayName + ' existiert bereits');
                that.moveUsernamePosition(resultArray[index].displayName, resultArray[index].coord);

                if (!that.isUserPositionColor(resultArray[index].displayName, resultArray[index].displayColor)) {
                    that.setUserPositionColor(resultArray[index].displayName, resultArray[index].displayColor);
                }
            }
        }

        that.clearOldPositions(resultArray);
    }

    $.support.cors = true;
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: 'http://beta.criticalmass.cm:1338/api/positions/hamburg?token=123',
        cache: false,
        success: callback
    });
};

MapPositions.prototype.isUserPositionColor = function(displayName, displayColor)
{
    return this.positionsArray[displayName].options.color == 'rgb(' + displayColor.red + ', ' + displayColor.green + ', ' + displayColor.blue + ')';
};

MapPositions.prototype.setUserPositionColor = function(displayName, displayColor)
{
    var userColor = 'rgb(' + displayColor.red + ', ' + displayColor.green + ', ' + displayColor.blue + ')';
    var circleOptions = {
        color: userColor,
        fillColor: userColor
    };

    this.positionsArray[displayName].setStyle(circleOptions);
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