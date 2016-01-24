define(['leaflet', 'MarkerEntity'], function() {
    CityRideEntity = function(cityTitle, cityName, citySlug, cityDescription, cityLatitude, cityLongitude, rideTitle, rideDescription, rideLatitude, rideLongitude, rideLocation, rideDate, rideTime, rideWeather) {
        this._cityTitle = cityTitle;
        this._cityName = cityName;
        this._citySlug = citySlug;
        this._cityDescription = cityDescription;
        this._cityLatitude = cityLatitude;
        this._cityLongitude = cityLongitude;
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

    CityEntity.prototype.buildPopup = function() {
        var html = '<h5>' + this._title + '</h5>';
        html += '<p>' + this._description + '</p>';

        return html;
    };

    return CityEntity;
});