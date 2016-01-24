define(['leaflet', 'MarkerEntity'], function() {
    PositionEntity = function() {

    };

    PositionEntity.prototype = new MarkerEntity();
    PositionEntity.prototype.constructor = PositionEntity;

    PositionEntity.prototype._color = null;
    PositionEntity.prototype._gravatarHash = null;


    PositionEntity.prototype.parseJson = function(position) {
        this._colorRed = position.displayColor.red;
        this._colorGreen = position.displayColor.green;
        this._colorBlue = position.displayColor.blue;

        this._latitude = position.coord.latitude;
        this._longitude = position.coord.longitude;

        this._gravatarHash = position.gravatarHash;
    };

    PositionEntity.prototype._initIcon = function() {
        this._icon = L.divIcon({
            iconSize: new L.Point(50, 50),
            className: 'user-position',
            html: '<div class="user-position-inline" style="background-image: url(https://www.gravatar.com/avatar/' + this._gravatarHash + '); border-color: ' + this.getColorString() + '"></div>'
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
        this._colorRed = position.displayColor.red;
        this._colorGreen = position.displayColor.green;
        this._colorBlue = position.displayColor.blue;

        var circleOptions = {
            color: this.getColorString(),
            fillColor: this._color
        };

        this._marker.setStyle(circleOptions);
    };

    return PositionEntity;
});