define(['leaflet', 'MarkerEntity'], function() {
    NoLocationRideEntity = function (title, description, latitude, longitude, location, date, time, weather) {
        this._title = title;
        this._description = description;
        this._latitude = latitude;
        this._longitude = longitude;
        this._location = location;
        this._date = date;
        this._time = time;
        this._weather = weather;
    };

    NoLocationRideEntity.prototype = new MarkerEntity();
    NoLocationRideEntity.prototype.constructor = NoLocationRideEntity;

    NoLocationRideEntity.prototype._title = null;
    NoLocationRideEntity.prototype._description = null;
    NoLocationRideEntity.prototype._location = null;
    NoLocationRideEntity.prototype._date = null;
    NoLocationRideEntity.prototype._time = null;
    NoLocationRideEntity.prototype._weather = null;

    NoLocationRideEntity.prototype._markerIconOptions = {
        iconUrl: '/bundles/calderacriticalmasssite/images/marker/marker-gray.png',
        iconRetinaUrl: '/bundles/calderacriticalmasssite/images/marker/marker-gray-2x.png'
    };

    NoLocationRideEntity.prototype._getPopupContent = function () {
        var content = '<h5>' + this._title + '</h5>';
        content += '<dl class="dl-horizontal">';
        content += '<dt>Datum:</dt><dd>' + this._date + '</dd>';
        content += '<dt>Uhrzeit:</dt><dd>' + this._time + ' Uhr</dd>';
        content += '<dt>Treffpunkt:</dt><dd><em>noch nicht bekannt</em></dd>';

        if (this._weather) {
            content += '<dt>Wetter:</dt><dd>' + this._weather + '</dd>';
        }

        content += '</dl>';
        content += '<p>' + this._description + '</p>';

        return content;
    };

    return NoLocationRideEntity;
});