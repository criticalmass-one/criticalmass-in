define(['leaflet', 'PositionEntity'], function (L) {
    MapPositions = function (context, options) {
        this._options = options;

        this._container = new Container();
    };

    MapPositions.prototype._options = null;
    MapPositions.prototype._map = null;
    MapPositions.prototype._timer = null;
    MapPositions.prototype._container = null;
    MapPositions.prototype._offlineCallback = null;

    MapPositions.prototype.addToMap = function (map) {
        this._map = map;

        this._container.addToMap(this._map);
    };

    MapPositions.prototype.start = function () {
        this._drawPositions();

        var that = this;
        this._timer = window.setInterval(function () {
            that._drawPositions();

        }, 15000);
    };

    MapPositions.prototype.setOfflineCallback = function (offlineCallback) {
        this._offlineCallback = offlineCallback;
    };

    MapPositions.prototype.addToControl = function (layerArray, title) {
        this._container.addToControl(layerArray, title);
    };

    MapPositions.prototype.getLayer = function () {
        return this._container.getLayer();
    };

    MapPositions.prototype._clearOldPositions = function (resultArray) {
        for (var existingIdentifier in this._container.getList()) {
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
        this._container.removeEntity(identifier);
    };

    MapPositions.prototype._createUsernamePosition = function (position) {
        var positionElement = new PositionEntity();
        positionElement.parseJson(position);

        positionElement.addToContainer(this._container, position.identifier);
    };

    MapPositions.prototype._moveUsernamePosition = function (identifier, coord) {
        this._container.getEntity(identifier).setLatLng([coord.latitude, coord.longitude]);
    };

    MapPositions.prototype._drawPositions = function () {
        var that = this;

        function successCallback(ajaxResultData) {
            var resultArray = ajaxResultData.result;

            for (var index in resultArray) {
                var identifier = resultArray[index].identifier;

                if (!that._container.hasEntity(identifier)) {
                    that._createUsernamePosition(resultArray[index]);
                } else {
                    that._moveUsernamePosition(resultArray[index].identifier, resultArray[index].coord);

                    if (!that._isUserPositionColor(resultArray[index].identifier, resultArray[index].displayColor) == false) {
                        that._setUserPositionColor(resultArray[index].identifier, resultArray[index].displayColor);
                    }
                }
            }

            that._clearOldPositions(resultArray);
        }

        function errorCallback() {
            if (that._offlineCallback) {
                that._offlineCallback();
            }
        }

        var apiUrl = this._options.apiUrl;
        var accessToken = this._options.apiAccessToken;

        $.support.cors = true;
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: apiUrl,
            cache: false,
            headers: {
                'X-CriticalMassIn-Access-Token': accessToken
            },
            success: successCallback,
            error: errorCallback
        });
    };

    MapPositions.prototype._isUserPositionColor = function (identifier, displayColor) {

        if (!this._container.hasEntity(identifier)) {
            return null;
        }

        return this._container.getEntity(identifier).getColorString()
            ==
            'rgb(' + displayColor.red + ', ' + displayColor.green + ', ' + displayColor.blue + ')';
    };

    MapPositions.prototype._setUserPositionColor = function (identifier, displayColor) {
        this._container.getEntity(identifier).setColor(displayColor);
    };

    MapPositions.prototype.countPositions = function () {
        return this._container.countEntities();
    };

    MapPositions.prototype.getBounds = function () {
        return this._container.getBounds();
    };

    MapPositions.prototype.getLatestLatLng = function () {
        var positionList = this._container.getList();

        var latestPosition = positionList[0];

        if (latestPosition) {
            return latestPosition.getLatLng();
        }

        return null;
    };

    return MapPositions;
});
