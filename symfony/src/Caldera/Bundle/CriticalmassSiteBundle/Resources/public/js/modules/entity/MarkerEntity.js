define(['BaseEntity', 'leaflet', 'Modal', 'leaflet-extramarkers'], function() {
    MarkerEntity = function () {

    };

    MarkerEntity.prototype = new BaseEntity();
    MarkerEntity.prototype.constructor = MarkerEntity;

    MarkerEntity.prototype._latitude = null;
    MarkerEntity.prototype._longitude = null;
    MarkerEntity.prototype._marker = null;
    MarkerEntity.prototype._icon = null;
    MarkerEntity.prototype._modal = null;

    // this should be extended
    MarkerEntity.prototype._initIcon = function() {
    };

    // this should be extended, too
    MarkerEntity.prototype._setupModalContent = function() {

    };

    MarkerEntity.prototype._initPopup = function() {
        this._modal = new Modal();
        this._modal.setSize('md');

        this._setupModalContent();

        var that = this;

        this._marker.on('click', function() {
            that._modal.show();
        });
    };

    MarkerEntity.prototype._createMarker = function() {
        if (!this._icon) {
            this._initIcon();
        }

        if (!this._marker) {
            this._marker = L.marker(
                [
                    this._latitude,
                    this._longitude
                ], {
                    icon: this._icon
                }
            );

            this._initPopup();
        }
    };

    MarkerEntity.prototype.addToMap = function(map) {
        if (this.hasLocation()) {
            this._createMarker();

            this._marker.addTo(map.map);
        }
    };

    MarkerEntity.prototype.addToLayer = function(markerLayer) {
        if (this.hasLocation()) {
            this._createMarker();

            markerLayer.addLayer(this._marker);
        }
    };

    MarkerEntity.prototype.openPopup = function() {
        this._modal.show();
    };

    MarkerEntity.prototype.getMarker = function() {
        if (!this._marker) {
            this._createMarker();
        }

        return this._marker;
    };

    MarkerEntity.prototype.getLayer = function() {
        return this.getMarker();
    };

    MarkerEntity.prototype.hasLocation = function () {
        return (this._latitude != null && this._longitude != null && this._latitude != 0 && this._longitude != 0);
    };

    MarkerEntity.prototype.getLatitude = function() {
        return this._latitude;
    };

    MarkerEntity.prototype.getLongitude = function() {
        return this._longitude;
    };

    MarkerEntity.prototype.getLatLng = function() {
        return [this._latitude, this._longitude];
    };

    MarkerEntity.prototype.setLatitude = function(latitude) {
        this._latitude = latitude;

        if (this._marker) {
            this._marker.setLatLng([this._latitude, this._longitude]);
        }

        return this;
    };

    MarkerEntity.prototype.setLongitude = function(longitude) {
        this._longitude = longitude;

        if (this._marker) {
            this._marker.setLatLng([this._latitude, this._longitude]);
        }

        return this;
    };

    MarkerEntity.prototype.setLatLng = function(latLng) {
        this._latitude = latLng.lat;
        this._longitude = latLng.lng;

        if (this._marker) {
            this._marker.setLatLng(latLng);
        }

        return this;
    };

    MarkerEntity.prototype.on = function(event, callback) {
        this._marker.on(event, callback);
    };

    return MarkerEntity;
});
