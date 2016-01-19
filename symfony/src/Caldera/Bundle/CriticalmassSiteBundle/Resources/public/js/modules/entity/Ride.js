define(['leaflet'], function() {
    Ride = function (title, description, latitude, longitude, location, date, time, weather) {
        this._title = title;
        this._description = description;
        this._latitude = latitude;
        this._longitude = longitude;
        this._location = location;
        this._date = date;
        this._time = time;
        this._weather = weather;

        this._initIcon();
    };

    Ride.prototype._title = null;
    Ride.prototype._description = null;
    Ride.prototype._latitude = null;
    Ride.prototype._longitude = null;
    Ride.prototype._location = null;
    Ride.prototype._date = null;
    Ride.prototype._time = null;
    Ride.prototype._weather = null;
    Ride.prototype._marker = null;
    Ride.prototype._icon = null;

    Ride.prototype._initIcon = function() {
        this._icon = L.icon({
            iconUrl: '/images/marker/marker-red.png',
            iconRetinaUrl: '/images/marker/marker-red-2x.png',
            iconSize: [25, 41],
            iconAnchor: [13, 41],
            popupAnchor: [0, -36],
            shadowUrl: '/images/marker/defaultshadow.png',
            shadowSize: [41, 41],
            shadowAnchor: [13, 41]
        });
    };

    Ride.prototype.buildPopup = function () {
        var html = '<h5>' + this._title + '</h5>';
        html += '<dl class="dl-horizontal">';
        html += '<dt>Datum:</dt><dd>' + this._date + '</dd>';
        html += '<dt>Uhrzeit:</dt><dd>' + this._time + '</dd>';
        html += '<dt>Treffpunkt:</dt><dd>' + this._location + '</dd>';
        html += '</dl>';
        html += '<p>' + this._description + '</p>';

        return html;
    };

    Ride.prototype.hasLocation = function () {
        return (this._latitude != null && this._longitude != null && this._location != null && this._location != '');
    };

    Ride.prototype._createMarker = function() {
        if (!this._marker) {
            this._marker = L.marker(
                [
                    this._latitude,
                    this._longitude
                ], {
                    icon: this._icon
                }
            );
        }
    };

    Ride.prototype.addToMap = function(map) {
        if (this.hasLocation()) {
            this._createMarker();

            this._marker.addTo(map.map);
        }
    };

    Ride.prototype.addToLayer = function(markerLayer) {
        if (this.hasLocation()) {
            this._createMarker();

            markerLayer.addLayer(this._marker);
        }
    };

    Ride.prototype.addToContainer = function(container) {
        container.addEntity(this);
    };

    Ride.prototype.openPopup = function() {
        if (this.hasLocation()) {
            this._marker.openPopup();
        }
    };

    Ride.prototype.getLatitude = function() {
        return this._latitude;
    };

    Ride.prototype.getLongitude = function() {
        return this._longitude;
    };

    return Ride;
});