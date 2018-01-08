define(['Marker'], function () {
    PositionMarker = function (latLng, draggable, username, backgroundImage) {
        this._latLng = latLng;
        this._draggable = draggable;

        this._username = username;
        this._backgroundImage = backgroundImage;
    };

    PositionMarker.prototype = new Marker();
    PositionMarker.prototype.constructor = PositionMarker;

    PositionMarker.prototype._colorRed = 0;
    PositionMarker.prototype._colorGreen = 0;
    PositionMarker.prototype._colorBlue = 0;

    PositionMarker.prototype.setColorRed = function (colorRed) {
        this._colorRed = colorRed;
    };

    PositionMarker.prototype.setColorGreen = function (colorGreen) {
        this._colorGreen = colorGreen;
    };

    PositionMarker.prototype.setColorBlue = function (colorBlue) {
        this._colorBlue = colorBlue;
    };

    PositionMarker.prototype.getColorString = function () {
        return 'rgb(' + this._colorRed + ', ' + this._colorGreen + ', ' + this._colorBlue + ');';
    };

    PositionMarker.prototype._getHTML = function () {
        return '<div class="user-position-inline" style="border-color: ' + this.getColorString() + '; background-image: url(' + this._backgroundImage + ');"></div>';
    };

    PositionMarker.prototype._initIcon = function () {
        this._icon = L.divIcon({
            iconSize: new L.Point(50, 50),
            className: 'user-position',
            html: this._getHTML()
        });
    };

    return PositionMarker;
});