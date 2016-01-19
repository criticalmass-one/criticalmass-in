define(['leaflet'], function() {
    City = function(title, name, slug, description, latitude, longitude) {
        this._title = title;
        this._name = name;
        this._slug = slug;
        this._description = description;
        this._latitude = latitude;
        this._longitude = longitude;

        this._initIcon();
    };

    City.prototype._title = null;
    City.prototype._name = null;
    City.prototype._slug = null;
    City.prototype._description = null;
    City.prototype._latitude = null;
    City.prototype._longitude = null;
    City.prototype._marker = null;
    City.prototype._icon = null;

    City.prototype._initIcon = function() {
        this._icon = L.icon({
            iconUrl: '/bundles/calderacriticalmasssite/images/marker/marker-blue.png',
            iconRetinaUrl: '/bundles/calderacriticalmasssite/images/marker/marker-blue-2x.png',
            iconSize: [25, 41],
            iconAnchor: [13, 41],
            popupAnchor: [0, -36],
            shadowUrl: '/bundles/calderacriticalmasssite/images/marker/defaultshadow.png',
            shadowRetinaUrl: '/bundles/calderacriticalmasssite/images/marker/defaultshadow.png',
            shadowSize: [41, 41],
            shadowAnchor: [13, 41]
        });
    };

    City.prototype._createMarker = function() {
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

    City.prototype.buildPopup = function() {
        var html = '<h5>' + this._title + '</h5>';
        html += '<p>' + this._description + '</p>';

        return html;
    };

    City.prototype.addToMap = function(map) {
        this._createMarker();

        this._marker.addTo(map.map);
    };

    City.prototype.addToLayer = function(markerLayer) {
        this._createMarker();

        markerLayer.addLayer(this._marker);
    };

    City.prototype.addToContainer = function(container) {
        container.addEntity(this);
    };

    City.prototype.getLatitude = function() {
        return this._latitude;
    };

    City.prototype.getLongitude = function() {
        return this._longitude;
    };

    return City;
});