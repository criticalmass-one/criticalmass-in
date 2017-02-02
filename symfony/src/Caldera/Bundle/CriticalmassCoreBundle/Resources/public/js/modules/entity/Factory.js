define([], function () {

    Factory = function () {
    };

    Factory.prototype.createRide = function (rideJson) {
        var rideEntity = new RideEntity();

        rideEntity = this._transferProperties(rideEntity, rideJson);

        return rideEntity;
    };

    Factory.prototype.createLiveRide = function (liveRideJson) {
        var liveRideEntity = new LiveRideEntity();

        liveRideEntity = this._transferProperties(liveRideEntity, liveRideJson);

        return liveRideEntity;
    };

    Factory.prototype.createEvent = function (eventJson) {
        var eventEntity = new EventEntity();

        eventEntity = this._transferProperties(eventEntity, eventJson);

        return eventEntity;
    };

    Factory.prototype.createSubride = function (subrideJson) {
        var subrideEntity = new SubrideEntity();

        subrideEntity = this._transferProperties(subrideEntity, subrideJson);

        return subrideEntity;
    };

    Factory.prototype.createCity = function (cityJson) {
        var cityEntity = new CityEntity();

        cityEntity = this._transferProperties(cityEntity, cityJson);

        return cityEntity;
    };

    Factory.prototype.createPhoto = function (photoJson) {
        var photoEntity = new PhotoEntity();

        photoEntity = this._transferProperties(photoEntity, photoJson);

        return photoEntity;
    };

    Factory.prototype.createIncident = function (incidentJson) {
        var incidentEntity = new IncidentEntity();

        incidentEntity = this._transferProperties(incidentEntity, incidentJson);

        return incidentEntity;
    };

    Factory.prototype.createTrack = function (trackJson) {
        var trackEntity = new TrackEntity();

        trackEntity = this._transferProperties(trackEntity, trackJson);

        return trackEntity;
    };

    Factory.prototype.createUser = function (userJson) {
        //alert(JSON.stringify(userJson));
        var userEntity = new UserEntity();

        userEntity = this._transferProperties(userEntity, userJson);

        return userEntity;
    };

    Factory.prototype._transferProperties = function (entity, data) {
        var object = null;

        if (data !== null && typeof data === 'object') {
            object = data;
        } else {
            object = JSON.parse(data);
        }

        for (var property in object) {
            if (object.hasOwnProperty(property)) {
                entityProperty = property.charAt(0).toLowerCase() + property.slice(1);

                var prefix = '';

                if (entityProperty.charAt(0) != '_') {
                    prefix = '_';
                }

                if (entityProperty == 'timestamp') {
                    entity[prefix + entityProperty] = new Date(object[property] * 1000);
                } else if (entityProperty == 'city') {
                    entity[prefix + entityProperty] = this.createCity(object[property]);
                } else if (entityProperty == 'user') {
                    entity[prefix + entityProperty] = this.createUser(object[property]);
                } else {
                    entity[prefix + entityProperty] = object[property];
                }
            }
        }

        return entity;
    };

    return new Factory;
});
