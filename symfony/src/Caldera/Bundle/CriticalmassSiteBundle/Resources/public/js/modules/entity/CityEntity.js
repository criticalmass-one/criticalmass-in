define(['leaflet', 'MarkerEntity'], function() {
    CityEntity = function(title, name, slug, description, latitude, longitude) {
        this._title = title;
        this._name = name;
        this._slug = slug;
        this._description = description;
        this._latitude = latitude;
        this._longitude = longitude;
    };

    CityEntity.prototype = new MarkerEntity();
    CityEntity.prototype.constructor = CityEntity;

    CityEntity.prototype._markerIconOptions = {
        iconUrl: '/bundles/calderacriticalmasssite/images/marker/marker-red.png',
        iconRetinaUrl: '/bundles/calderacriticalmasssite/images/marker/marker-red-2x.png'
    };

    CityEntity.prototype._title = null;
    CityEntity.prototype._name = null;
    CityEntity.prototype._slug = null;
    CityEntity.prototype._description = null;

    CityEntity.prototype._getPopupContent = function () {
        var content = '<h5>' + this._title + '</h5>';
        content += '<p>' + this._description + '</p>';

        return content;
    };

    CityEntity.prototype.getSlug= function() {
        return this._slug;
    };

    return CityEntity;
});
