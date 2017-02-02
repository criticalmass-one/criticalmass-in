define(['Map', 'TrackEntity', 'SnapablePhotoMarker', 'PhotoEntity', 'Container'], function () {
    RelocatePhotoPage = function (context, options) {
        this._options = options;


        this._photoContainer = new Container();

        this._initMap();
    };

    RelocatePhotoPage.prototype._track = null;

    RelocatePhotoPage.prototype.init = function () {

        this._photoContainer.addToMap(this._map);
    };

    RelocatePhotoPage.prototype._initMap = function () {
        this._map = new Map('map');
    };

    RelocatePhotoPage.prototype.setTrack = function (polylineLatLngs, colorRed, colorGreen, colorBlue) {
        this._track = new TrackEntity();
        this._track.setPolyline(polylineLatLngs, colorRed, colorGreen, colorBlue);
        this._track.addToMap(this._map);

        this._map.fitBounds(this._track.getBounds());
    };

    RelocatePhotoPage.prototype.addPhoto = function (photoId, latitude, longitude, description, dateTime, filename) {
        var photo = new PhotoEntity(photoId, latitude, longitude, description, dateTime, filename);

        var entityId = this._photoContainer.countEntities() + 1;

        photo.addToContainer(this._photoContainer, entityId);

        var that = this;
    };

    RelocatePhotoPage.prototype.relocateByTime = function (offsetSeconds) {
        for (var index = 0; index < this._photoContainer.countEntities(); ++index) {
            var photo = this._photoContainer.getEntity(index);

            //if (photo.timestamp != ignoreTimestamp)
            //{
            var oldDateTime = photo.getDateTime();
            var timestamp = oldDateTime.getTime() + offsetSeconds;

            var newLatLng = findLatLngForTimestamp(newTimestamp);

            photo.setLatLng(newLatLng);
            //}
        }
    };

    function findLatLngForTimestamp(timestamp) {
        var smallerTimestamp = null;

        for (var coordTimestamp in coords) {
            if (smallerTimestamp == null) {
                smallerTimestamp = coordTimestamp;

            }
            else if (coordTimestamp < timestamp) {
                smallerTimestamp = coordTimestamp;
            }
            else {
                break;
            }
        }

        return coords[smallerTimestamp];
    }

    function findTimestampForLatLng(latLng) {
        var smallestDistance = null;
        var smallestTimestamp = null;

        for (var timestamp in coords) {
            var coord = coords[timestamp];

            if (smallestDistance == null) {
                smallestDistance = coord.distanceTo(latLng);
                smallestTimestamp = timestamp;
            }
            else {
                var distance = coord.distanceTo(latLng);

                if (distance < smallestDistance) {
                    smallestDistance = distance;
                    smallestTimestamp = timestamp;
                }
            }

        }

        return smallestTimestamp;
    }

    return RelocatePhotoPage;
});