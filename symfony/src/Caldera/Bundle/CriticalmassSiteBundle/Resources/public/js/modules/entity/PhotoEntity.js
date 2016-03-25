define(['MarkerEntity', 'leaflet-extramarkers'], function() {
    PhotoEntity = function(id, latitude, longitude, description, dateTime, filename) {
        this._id = id;
        this._latitude = latitude;
        this._longitude = longitude;
        this._description = description;
        this._dateTime = dateTime;
        this._filename = filename;
    };

    PhotoEntity.prototype = new MarkerEntity();
    PhotoEntity.prototype.constructor = PhotoEntity;

    PhotoEntity.prototype._id = null;
    PhotoEntity.prototype._description = null;
    PhotoEntity.prototype._dateTime = null;
    PhotoEntity.prototype._filename = null;

    PhotoEntity.prototype._initIcon = function() {
        this._icon = L.ExtraMarkers.icon({
            icon: 'fa-camera',
            markerColor: 'yellow',
            shape: 'square',
            prefix: 'fa'
        });
    };

    PhotoEntity.prototype.getId = function() {
        return this._id;
    };

    PhotoEntity.prototype.getFilename = function() {
        return this._filename;
    };

    PhotoEntity.prototype._initPopup = function() {
        return null;
    };

    PhotoEntity.prototype.openPopup = function() {
        return null;
    };

    return PhotoEntity;
});
