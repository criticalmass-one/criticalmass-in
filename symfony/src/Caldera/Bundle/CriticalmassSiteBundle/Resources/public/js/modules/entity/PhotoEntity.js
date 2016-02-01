define(['MarkerEntity'], function() {
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

    PhotoEntity.prototype._markerIconOptions = {
        iconUrl: '/bundles/calderacriticalmasssite/images/marker/marker-yellow.png',
        iconRetinaUrl: '/bundles/calderacriticalmasssite/images/marker/marker-yellow-2x.png'
    };

    PhotoEntity.prototype._id = null;
    PhotoEntity.prototype._description = null;
    PhotoEntity.prototype._dateTime = null;
    PhotoEntity.prototype._filename = null;

    PhotoEntity.prototype.getId = function() {
        return this._id;
    };

    PhotoEntity.prototype.getFilename = function() {
        return this._filename;
    };

    return PhotoEntity;
});
