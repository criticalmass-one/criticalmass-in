define(['leaflet', 'MarkerEntity'], function() {
    PositionEntity = function() {

    };

    PositionEntity.prototype = new MarkerEntity();
    PositionEntity.prototype.constructor = PositionEntity;

    PositionEntity.prototype._color = null;
    PositionEntity.prototype._avatarUrl = null;


    PositionEntity.prototype.parseJson = function(position) {
        this._colorRed = position.displayColor.red;
        this._colorGreen = position.displayColor.green;
        this._colorBlue = position.displayColor.blue;

        this._latitude = position.coord.latitude;
        this._longitude = position.coord.longitude;

        this._avatarUrl = position.user.avatarUrl;
    };

    PositionEntity.prototype._getHTML = function() {
        return '<div class="user-position-inline" style="background-image: url(' + this._avatarUrl + '); border-color: ' + this.getColorString() + '"></div>';
    };

    PositionEntity.prototype._initIcon = function() {
        this._icon = L.divIcon({
            iconSize: new L.Point(50, 50),
            className: 'user-position',
            html: this._getHTML()
        });
    };

    PositionEntity.prototype.buildPopup = function() {
        var html = '<h5>' + this._title + '</h5>';
        html += '<p>' + this._description + '</p>';

        return html;
    };

    PositionEntity.prototype.getColorString = function() {
        return 'rgb(' + Math.round(this._colorRed) + ', ' + Math.round(this._colorGreen) + ', ' + Math.round(this._colorBlue) + ')';
    };

    PositionEntity.prototype.setColor = function(color) {
        this._colorRed = color.red;
        this._colorGreen = color.green;
        this._colorBlue = color.blue;

        this._initIcon();
    };

    return PositionEntity;
});