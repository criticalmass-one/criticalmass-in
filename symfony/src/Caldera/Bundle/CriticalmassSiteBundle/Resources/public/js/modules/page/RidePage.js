define(['Map', 'Container', 'CityEntity', 'RideEntity', 'TrackEntity', 'MapLayerControl', 'PhotoEntity'], function() {

    RidePage = function(context, options) {
        this._initMap();
        this._initContainers();
        this._initLayers();
        this._initLayerControl();
        this._initTrackToggleEvent();
    };

    RidePage.prototype._map = null;
    RidePage.prototype._ride = null;
    RidePage.prototype._city = null;
    RidePage.prototype._subrideContainer = null;
    RidePage.prototype._layerControl = null;
    RidePage.prototype._layers = null;

    RidePage.prototype.init = function() {

    };

    RidePage.prototype._initLayerControl = function() {
        this._layers = [];

        this._rideContainer.addToControl(this._layers, 'Tour');
        this._cityContainer.addToControl(this._layers, 'Städte');
        this._subrideContainer.addToControl(this._layers, 'Mini-Masses');
        this._trackContainer.addToControl(this._layers, 'Tracks');
        this._photoContainer.addToControl(this._layers, 'Fotos');

        this._layerControl = new MapLayerControl();
        this._layerControl.setLayers(this._layers);
        this._layerControl.init();
        this._layerControl.addTo(this._map);
    };

    RidePage.prototype._initMap = function() {
        this._map = new Map('map');
    };

    RidePage.prototype._initContainers = function() {
        this._subrideContainer = new Container();
        this._trackContainer = new Container();
        this._cityContainer = new Container();
        this._rideContainer = new Container();
        this._photoContainer = new Container();
    };

    RidePage.prototype._initLayers = function() {
        this._subrideContainer.addToMap(this._map);
        this._trackContainer.addToMap(this._map);
        this._cityContainer.addToMap(this._map);
        this._rideContainer.addToMap(this._map);
        this._photoContainer.addToMap(this._map);
    };

    RidePage.prototype._initTrackToggleEvent = function() {
        var that = this;

        $('.track-visibility-toggle').on('click', function() {
            var trackId = $(this).data('track-id');

            that._toggleTrack(trackId);
        });
    };

    RidePage.prototype.addCity = function(cityName, cityTitle, slug, description, latitude, longitude) {
        this._city = new CityEntity(cityName, cityTitle, slug, description, latitude, longitude);

        this._city.addToContainer(this._cityContainer);
    };

    RidePage.prototype.addRide = function(title, description, latitude, longitude, location, date, time, weatherForecast) {
        this._ride = new RideEntity(title, description, latitude, longitude, location, date, time, weatherForecast);

        this._ride.addToContainer(this._rideContainer);
    };

    RidePage.prototype.addTrack = function(trackId, polylineLatLngs, colorRed, colorGreen, colorBlue) {
        var track = new TrackEntity();
        track.setPolyline(polylineLatLngs, colorRed, colorGreen, colorBlue);

        track.addToContainer(this._trackContainer, trackId);
    };

    RidePage.prototype._toggleTrack = function(trackId) {
        this._trackContainer.toggleIndexEntityInLayer(trackId);
    };

    RidePage.prototype.addPhoto = function(photoId, latitude, longitude, description, dateTime, filename) {
        var photo = new PhotoEntity(photoId, latitude, longitude, description, dateTime, filename);

        var entityId = this._photoContainer.countEntities() + 1;

        photo.addToContainer(this._photoContainer, entityId);

        var that = this;

        photo.on('click', function() {
            that._showPhoto(entityId);
        });
    };

    RidePage.prototype._showPhoto = function(entityId) {
        var photo = this._photoContainer.getEntity(entityId);

        alert('show photo with entity id ' + entityId);

        var $modal = $('#photo-view-modal');
        $modal.find('img').attr('src', photo.getFilename());
        $modal.find('img').attr('id', 'photo-' + entityId);

        if (!$modal.hasClass('in')) {
            $modal.modal();
        }

        this._updatePhotoViewNavigation(entityId);
    };

    RidePage.prototype._updatePhotoViewNavigation = function(entityId) {
        var $modal = $('#photo-view-modal');
        var $nextPhotoButton = $modal.find('li.next');
        var $previousPhotoButton = $modal.find('li.previous');

        var previousPhotoEntityId = this._photoContainer.getPreviousIndex(entityId);
        var nextPhotoEntityId = this._photoContainer.getNextIndex(entityId);

        var that = this;

        $('body').off('keydown').on('keydown', function(e) {
            if (nextPhotoEntityId && (e.keyCode || e.which) == 39) {
                that._showPhoto(nextPhotoEntityId);
            }

            if (previousPhotoEntityId && (e.keyCode || e.which) == 37) {
                that._showPhoto(previousPhotoEntityId);
            }
        });

        if (previousPhotoEntityId) {
            $previousPhotoButton.find('a').off('click').on('click', function (element) {
                element.preventDefault();
                that._showPhoto(previousPhotoEntityId);
            });

            $previousPhotoButton.removeClass('disabled');
        } else {
            $previousPhotoButton.addClass('disabled');
            $previousPhotoButton.find('a').off('click');
        }

        if (nextPhotoEntityId) {
            $nextPhotoButton.find('a').off('click').on('click', function(element) {
                element.preventDefault();

                that._showPhoto(nextPhotoEntityId);
            });

            $nextPhotoButton.removeClass('disabled');
        } else {
            $nextPhotoButton.addClass('disabled');
            $nextPhotoButton.find('a').off('click');
        }
    };

    RidePage.prototype.focus = function() {
        if (this._ride.hasLocation()) {
            this._map.setView([this._ride.getLatitude(), this._ride.getLongitude()], 10);
        } else {
            this._map.setView([this._city.getLatitude(), this._city.getLongitude()], 10);
        }
    };

    return RidePage;
});
