define(['RideEntity', 'CityEntity'], function() {

    Factory = function () {
    };

    Factory.prototype.createRide = function(rideJson) {
        var rideEntity = new RideEntity();

        rideEntity = this._transferProperties(rideEntity, rideJson);

        return rideEntity;
    };

    Factory.prototype.createCity = function(cityJson) {
        var cityEntity = new CityEntity();

        cityEntity = this._transferProperties(cityEntity, cityJson);

        return cityEntity;
    };

    Factory.prototype._transferProperties = function(entity, data) {
        var object = null;

        if (data !== null && typeof data === 'object') {
            object = data;
        } else {
            object = JSON.parse(data);
        }

        for (var property in object) {
            if (object.hasOwnProperty(property)) {
                entityProperty = property.charAt(0).toLowerCase() + property.slice(1);

                if (entityProperty == 'timestamp') {
                    entity['_' + entityProperty] = new Date(object[property] * 1000);
                } else if (entityProperty == 'city') {
                    entity['_' + entityProperty] = this.createCity(object[property]);
                } else {
                    entity['_' + entityProperty] = object[property];
                }

            }
        }

        return entity;
    };

    return Factory;
});
