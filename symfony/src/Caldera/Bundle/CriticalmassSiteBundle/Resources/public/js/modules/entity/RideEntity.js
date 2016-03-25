define(['leaflet', 'MarkerEntity', 'leaflet-extramarkers'], function() {
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

    RideEntity.prototype._initIcon = function() {
        this._icon = L.ExtraMarkers.icon({
            icon: 'fa-bicycle',
            markerColor: 'red',
            shape: 'round',
            prefix: 'fa'
        });
    };

    RideEntity.prototype._setupModalContent = function () {
        this._modal.setTitle(this._title);

        var content = '<dl class="dl-horizontal">';
        content += '<dt>Datum:</dt><dd>' + this._date + '</dd>';
        content += '<dt>Uhrzeit:</dt><dd>' + this._time + ' Uhr</dd>';
        content += '<dt>Treffpunkt:</dt><dd>' + this._location + '</dd>';

        if (this._weather) {
            content += '<dt>Wetter:</dt><dd>' + this._weather + '</dd>';
        }

        content += '</dl>';
        content += '<p>' + this._description + '</p>';

        this._modal.setBody(content);
    };

    RideEntity.prototype.getDate = function() {
        return this._date;
    };

    return RideEntity;
});