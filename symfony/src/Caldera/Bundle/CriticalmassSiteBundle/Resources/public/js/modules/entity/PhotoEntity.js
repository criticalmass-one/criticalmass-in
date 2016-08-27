define(['MarkerEntity', 'leaflet-extramarkers'], function() {
    PhotoEntity = function() {
    };

    PhotoEntity.prototype = new MarkerEntity();
    PhotoEntity.prototype.constructor = PhotoEntity;

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

    PhotoEntity.prototype.setFilename = function(filename) {
        this._filename = filename;
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

    PhotoEntity.prototype.getDateTime = function() {
        return this._dateTime;
    };

    return PhotoEntity;
});
