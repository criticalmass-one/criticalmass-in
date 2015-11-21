MapPositions = function()
{

};

MapPositions.prototype.map = null;

MapPositions.prototype.layerGroup = null;

MapPositions.prototype.positionsArray = new Array();

MapPositions.prototype.positionsCounter = 0;

MapPositions.prototype.timer = null;

MapPositions.prototype.addTo = function(map) {
    this.map = map;

    this.layerGroup = L.layerGroup();
    this.layerGroup.addTo(this.map.map);
};

MapPositions.prototype.start = function() {
    this.drawPositions();

    var this2 = this;
    this.timer = window.setInterval(function() {
        this2.drawPositions();

    }, 5000);
};

MapPositions.prototype.clearOldPositions = function(identifier) {
    for (var existingIdentifier in this.positionsArray) {
        var found = false;

        for (var index in resultArray) {
            if (existingIdentifier == resultArray[index].identifier) {
                found = true;
                break;
            }
        }

        if (!found) {
            this.removeUsernamePosition(existingIdentifier);
        }
    }
};

MapPositions.prototype.removeUsernamePosition = function(identifier) {
    this.layerGroup.removeLayer(this.positionsArray[identifier]);
    delete this.positionsArray[identifier];
    --this.positionsCounter;
};

MapPositions.prototype.createUsernamePosition = function(position) {
    var userColor = 'rgb(' + position.displayColor.red + ', ' + position.displayColor.green + ', ' + position.displayColor.blue + ')';

    var myIcon = L.divIcon({
        iconSize: new L.Point(50, 50),
        className: 'user-position',
        html: '<div class="user-position-inline" style="background-image: url(https://www.gravatar.com/avatar/' + position.gravatarHash + '); border-color: ' + userColor +'"></div>'
    });

    var markerLatLng = [position.coord.latitude, position.coord.longitude];
    
    var marker = L.marker(markerLatLng, {icon: myIcon});
    
    //circle.addTo(this.layerGroup);
    marker.addTo(this.map.map);
    marker.bindPopup(position.displayName);

    this.positionsArray[position.identifier] = marker;
    ++this.positionsCounter;
    //console.log('erstelle ' + position.displayName);
    console.log('Jetzt ' + this.positionsCounter + ' Positionen gespeichert');
};

MapPositions.prototype.moveUsernamePosition = function(identifier, coord) {
    this.positionsArray[identifier].setLatLng([coord.latitude, coord.longitude]);
};

MapPositions.prototype.drawPositions = function() {
    /* yeah: normally this should work this the context option of the jquery stuff, but somehow it does not, so we use
    this nasty workaround here. */
    var that = this;
    
    function callback(ajaxResultData)
    {
        console.log('Bislang ' + that.positionsCounter + ' Positionen gespeichert');
        var resultArray = ajaxResultData.result;
        
        for (var index in resultArray) {
            if (!that.positionsArray[resultArray[index].identifier]) {
                that.createUsernamePosition(resultArray[index]);
            } else {
                that.moveUsernamePosition(resultArray[index].identifier, resultArray[index].coord);

                /*if (!that.isUserPositionColor(resultArray[index].displayName, resultArray[index].displayColor)) {
                    that.setUserPositionColor(resultArray[index].displayName, resultArray[index].displayColor);
                }*/
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
        headers: {
            'X-CriticalMassIn-Access-Token': 123
        },
        success: callback
    });
};

MapPositions.prototype.isUserPositionColor = function(identifier, displayColor) {
    return this.positionsArray[identifier].options.color == 'rgb(' + displayColor.red + ', ' + displayColor.green + ', ' + displayColor.blue + ')';
};

MapPositions.prototype.setUserPositionColor = function(identifier, displayColor) {
    var userColor = 'rgb(' + displayColor.red + ', ' + displayColor.green + ', ' + displayColor.blue + ')';
    
    var circleOptions = {
        color: userColor,
        fillColor: userColor
    };

    this.positionsArray[identifier].setStyle(circleOptions);
};

MapPositions.prototype.getLatestPosition = function(requestedSlug) {
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

MapPositions.prototype.panToLatestPosition = function(requestedSlug) {
    var latestPosition = this.getLatestPosition(requestedSlug);

    this.map.panTo(latestPosition.getLatLng());
};