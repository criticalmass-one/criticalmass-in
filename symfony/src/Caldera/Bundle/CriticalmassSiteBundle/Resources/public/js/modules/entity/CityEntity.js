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

    CityEntity.prototype._initIcon = function() {
        this._icon = L.ExtraMarkers.icon({
            icon: 'fa-university',
            markerColor: 'blue',
            shape: 'round',
            prefix: 'fa'
        });
    };

    CityEntity.prototype._setupModalContent = function () {
        this._modal.setTitle(this._title);
        this._modal.setBody(this._description);
    };

    CityEntity.prototype.getSlug= function() {
        return this._slug;
    };

    return CityEntity;
});
