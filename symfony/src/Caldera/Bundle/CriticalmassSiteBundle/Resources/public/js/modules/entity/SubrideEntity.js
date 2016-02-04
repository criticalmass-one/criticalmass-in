define(['leaflet', 'MarkerEntity'], function() {
    SubrideEntity = function (subrideId, title, description, latitude, longitude, location, date, time, weather) {
        this._subrideId = subrideId;
        this._title = title;
        this._description = description;
        this._latitude = latitude;
        this._longitude = longitude;
        this._location = location;
        this._date = date;
        this._time = time;
        this._weather = weather;
    };

    SubrideEntity.prototype = new MarkerEntity();
    SubrideEntity.prototype.constructor = SubrideEntity;

    SubrideEntity.prototype._subrideId = null;
    SubrideEntity.prototype._title = null;
    SubrideEntity.prototype._description = null;
    SubrideEntity.prototype._location = null;
    SubrideEntity.prototype._date = null;
    SubrideEntity.prototype._time = null;
    SubrideEntity.prototype._weather = null;

    SubrideEntity.prototype._markerIconOptions = {
        iconUrl: '/bundles/calderacriticalmasssite/images/marker/marker-green.png',
        iconRetinaUrl: '/bundles/calderacriticalmasssite/images/marker/marker-green-2x.png'
    };

    SubrideEntity.prototype._getPopupContent = function () {
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

    return SubrideEntity;
});