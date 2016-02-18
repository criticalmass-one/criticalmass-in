define(['leaflet', 'MarkerEntity'], function() {
    RideEntity = function (title, description, latitude, longitude, location, date, time, weather) {
        this._title = title;
        this._description = description;
        this._latitude = latitude;
        this._longitude = longitude;
        this._location = location;
        this._date = date;
        this._time = time;
        this._weather = weather;
    };

    RideEntity.prototype = new MarkerEntity();
    RideEntity.prototype.constructor = RideEntity;

    RideEntity.prototype._title = null;
    RideEntity.prototype._description = null;
    RideEntity.prototype._location = null;
    RideEntity.prototype._date = null;
    RideEntity.prototype._time = null;
    RideEntity.prototype._weather = null;

    RideEntity.prototype._markerIconOptions = {
        iconUrl: '/bundles/calderacriticalmasssite/images/marker/marker-blue.png',
        iconRetinaUrl: '/bundles/calderacriticalmasssite/images/marker/marker-blue-2x.png'
    };

    RideEntity.prototype._getPopupContent = function () {
        var content = '<h5>' + this._title + '</h5>';
        content += '<dl class="dl-horizontal">';
        content += '<dt>Datum:</dt><dd>' + this._date + '</dd>';
        content += '<dt>Uhrzeit:</dt><dd>' + this._time + ' Uhr</dd>';
        content += '<dt>Treffpunkt:</dt><dd>' + this._location + '</dd>';

        if (this._weather) {
            content += '<dt>Wetter:</dt><dd>' + this._weather + '</dd>';
        }

        content += '</dl>';
        content += '<p>' + this._description + '</p>';

        return content;
    };

    RideEntity.prototype.getDate = function() {
        return this._date;
    };

    return RideEntity;
});