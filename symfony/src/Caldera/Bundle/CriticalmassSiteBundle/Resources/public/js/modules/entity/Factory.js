define(['CityEntity', 'TrackEntity', 'PhotoEntity', 'SubrideEntity'], function() {

    Factory = function () {
    };

    Factory.prototype.createRide = function(rideJson) {
        var rideEntity = new RideEntity();

        rideEntity = this._transferProperties(rideEntity, rideJson);

        return rideEntity;
    };

    Factory.prototype.createSubride = function(subrideJson) {
        var subrideEntity = new SubrideEntity();

        subrideEntity = this._transferProperties(subrideEntity, subrideJson);

        return subrideEntity;
    };

    Factory.prototype.createCity = function(cityJson) {
        var cityEntity = new CityEntity();

        cityEntity = this._transferProperties(cityEntity, cityJson);

        return cityEntity;
    };

    Factory.prototype.createPhoto = function(photoJson) {
        var photoEntity = new PhotoEntity();

        photoEntity = this._transferProperties(photoEntity, photoJson);

        return photoEntity;
    };

    Factory.prototype.createIncident = function(incidentJson) {
        var incidentEntity = new IncidentEntity();

        incidentEntity = this._transferProperties(incidentEntity, incidentJson);

        return incidentEntity;
    };

    Factory.prototype.createTrack = function(trackJson) {
        var trackEntity = new TrackEntity();

        trackEntity = this._transferProperties(trackEntity, trackJson);

        return trackEntity;
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

    return new Factory;
});
