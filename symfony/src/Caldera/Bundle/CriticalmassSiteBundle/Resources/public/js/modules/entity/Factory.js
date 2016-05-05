define(['RideEntity'], function() {

    Factory = function () {
    };

    Factory.prototype.createRide = function(rideJson) {
        var rideEntity = new RideEntity();

        rideEntity = this._transferProperties(rideEntity, rideJson);

        return rideEntity;
    };

    Factory.prototype._transferProperties = function(entity, jsonData) {
        var object = JSON.parse(jsonData);

        for (var property in object) {
            if (object.hasOwnProperty(property)) {
                entityProperty = property.charAt(0).toLowerCase() + property.slice(1);

                if (entityProperty == 'timestamp') {
                    entity['_' + entityProperty] = new Date(object[property] * 1000);
                } else {
                    entity['_' + entityProperty] = object[property];
                }

            }
        }

        return entity;
    };

    return Factory;
});
