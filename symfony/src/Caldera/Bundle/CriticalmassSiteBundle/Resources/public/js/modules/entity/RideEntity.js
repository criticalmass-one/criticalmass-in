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

    RideEntity.prototype.buildPopup = function () {
        var html = '<h5>' + this._title + '</h5>';
        html += '<dl class="dl-horizontal">';
        html += '<dt>Datum:</dt><dd>' + this._date + '</dd>';
        html += '<dt>Uhrzeit:</dt><dd>' + this._time + '</dd>';
        html += '<dt>Treffpunkt:</dt><dd>' + this._location + '</dd>';
        html += '</dl>';
        html += '<p>' + this._description + '</p>';

        return html;
    };

    RideEntity.prototype.openPopup = function() {
        if (this.hasLocation()) {
            this._marker.openPopup();
        }
    };

    return RideEntity;
});