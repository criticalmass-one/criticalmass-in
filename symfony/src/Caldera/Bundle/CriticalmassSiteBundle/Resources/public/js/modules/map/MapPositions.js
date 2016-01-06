define(['leaflet'], function(L) {
    MapPositions = function () {
        this._layerGroup = L.layerGroup();
    };

    MapPositions.prototype._map = null;

    MapPositions.prototype._layerGroup = null;

    MapPositions.prototype._positionsArray = [];

    MapPositions.prototype._positionsCounter = 0;

    MapPositions.prototype._timer = null;

    MapPositions.prototype.addTo = function (map) {
        this._map = map;

        this._layerGroup.addTo(this._map.map);
    };

    MapPositions.prototype.start = function () {
        this._drawPositions();

        var that = this;
        this._timer = window.setInterval(function () {
            that._drawPositions();

        }, 5000);
    };

    MapPositions.prototype.addControl = function(layerArray, title) {
        layerArray[title] = this._layerGroup;
    };

    MapPositions.prototype.getLayer = function() {
        return this._layerGroup;
    };

    MapPositions.prototype._clearOldPositions = function (resultArray) {
        for (var existingIdentifier in this._positionsArray) {
            var found = false;

            for (var index in resultArray) {
                if (existingIdentifier == resultArray[index].identifier) {
                    found = true;
                    break;
                }
            }

            if (!found) {
                this._removeUsernamePosition(existingIdentifier);
            }
        }
    };

    MapPositions.prototype._removeUsernamePosition = function (identifier) {
        this._layerGroup.removeLayer(this._positionsArray[identifier]);
        delete this._positionsArray[identifier];
        --this._positionsCounter;
    };

    MapPositions.prototype._createUsernamePosition = function (position) {
        var userColor = 'rgb(' + position.displayColor.red + ', ' + position.displayColor.green + ', ' + position.displayColor.blue + ')';

        var myIcon = L.divIcon({
            iconSize: new L.Point(50, 50),
            className: 'user-position',
            html: '<div class="user-position-inline" style="background-image: url(https://www.gravatar.com/avatar/' + position.gravatarHash + '); border-color: ' + userColor + '"></div>'
        });

        var markerLatLng = [position.coord.latitude, position.coord.longitude];

        var marker = L.marker(markerLatLng, {icon: myIcon});

        marker.addTo(this._layerGroup);
        //marker.addTo(this._map.map);
        marker.bindPopup(position.displayName);

        this._positionsArray[position.identifier] = marker;
        ++this._positionsCounter;
        //console.log('erstelle ' + position.displayName);
        console.log('Jetzt ' + this._positionsCounter + ' Positionen gespeichert');
    };

    MapPositions.prototype._moveUsernamePosition = function (identifier, coord) {
        this._positionsArray[identifier].setLatLng([coord.latitude, coord.longitude]);
    };

    MapPositions.prototype._drawPositions = function () {
        /* yeah: normally this should work this the context option of the jquery stuff, but somehow it does not, so we use
         this nasty workaround here. */
        var that = this;

        function callback(ajaxResultData) {
            console.log('Bislang ' + that._positionsCounter + ' Positionen gespeichert');
            var resultArray = ajaxResultData.result;

            for (var index in resultArray) {
                var identifier = resultArray[index].identifier;

                if (!that._positionsArray[identifier]) {
                    that._createUsernamePosition(resultArray[index]);
                } else {
                    that._moveUsernamePosition(resultArray[index].identifier, resultArray[index].coord);

                    if (!that._isUserPositionColor(resultArray[index].displayName, resultArray[index].displayColor) == false) {
                        that._setUserPositionColor(resultArray[index].displayName, resultArray[index].displayColor);
                    }
                }
            }

            that._clearOldPositions(resultArray);
        }

        var apiUrl = window.location.origin + '/api/positions/random';
        var accessToken = 123;

        $.support.cors = true;
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: apiUrl,
            cache: false,
            headers: {
                'X-CriticalMassIn-Access-Token': accessToken
            },
            success: callback
        });
    };

    MapPositions.prototype._isUserPositionColor = function (identifier, displayColor) {
        if (!this._positionsArray[identifier]) {
            return null;
        }

        return this._positionsArray[identifier].options.color
            ==
            'rgb(' + displayColor.red + ', ' + displayColor.green + ', ' + displayColor.blue + ')';
    };

    MapPositions.prototype._setUserPositionColor = function (identifier, displayColor) {
        var userColor = 'rgb(' + displayColor.red + ', ' + displayColor.green + ', ' + displayColor.blue + ')';

        var circleOptions = {
            color: userColor,
            fillColor: userColor
        };

        this._positionsArray[identifier].setStyle(circleOptions);
    };

    MapPositions.prototype._getLatestPosition = function (requestedSlug) {
        var maxTimestamp = 0;
        var maxTimestampIndex = 0;

        for (index in this._positionsArray) {
            var position = this._positionsArray[index];

            if (position.options.citySlug == requestedSlug && position.options.timestamp > maxTimestamp) {
                maxTimestamp = this._positionsArray[index].options.timestamp;
                maxTimestampIndex = index;
            }
        }

        return this._positionsArray[maxTimestampIndex];
    };

    MapPositions.prototype._panToLatestPosition = function (requestedSlug) {
        var latestPosition = this._getLatestPosition(requestedSlug);

        this._map.map.panTo(latestPosition.getLatLng());
    };

    return MapPositions;
});