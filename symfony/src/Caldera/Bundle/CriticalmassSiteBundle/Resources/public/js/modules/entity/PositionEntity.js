define(['leaflet', 'MarkerEntity'], function() {
    PositionEntity = function() {

    };

    PositionEntity.prototype = new MarkerEntity();
    PositionEntity.prototype.constructor = PositionEntity;

    PositionEntity.prototype._color = null;
    PositionEntity.prototype._gravatarHash = null;


    PositionEntity.prototype.parseJson = function(position) {
        this._color = 'rgb(' + position.displayColor.red + ', ' + position.displayColor.green + ', ' + position.displayColor.blue + ')';

        this._latitude = position.coord.latitude;
        this._longitude = position.coord.longitude;

        this._gravatarHash = position.gravatarHash;
        alert('parsejson');
    };

    PositionEntity.prototype._initIcon = function() {
        this._icon = L.divIcon({
            iconSize: new L.Point(50, 50),
            className: 'user-position',
            html: '<div class="user-position-inline" style="background-image: url(https://www.gravatar.com/avatar/' + this._gravatarHash + '); border-color: ' + this._color + '"></div>'
        });
    };

    PositionEntity.prototype.getMarker = function() {
        if (!this._marker) {
            this._createMarker();
        }

        return this._marker;
    };

    PositionEntity.prototype.buildPopup = function() {
        var html = '<h5>' + this._title + '</h5>';
        html += '<p>' + this._description + '</p>';

        return html;
    };

    return PositionEntity;
});