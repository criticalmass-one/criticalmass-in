define(['Map', 'Container', 'CityEntity', 'RideEntity', 'TrackEntity', 'SubrideEntity', 'MapLayerControl', 'PhotoEntity', 'PhotoViewModal'], function() {

    RidePage = function(context, options) {
        this._options = options;

        this._initMap();
        this._initContainers();
        this._initLayers();
        this._initLayerControl();
        this._initTrackToggleEvent();
        this._initPhotoViewModal();
        this._initSubrideEvents();
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

    RidePage.prototype._initPhotoViewModal = function() {
        var options = {
            photoViewPageUrl: this._options.photoViewPageUrl
        };

        this._photoViewModal = new PhotoViewModal(null, options);
        this._photoViewModal.setPhotoContainer(this._photoContainer);
        this._photoViewModal.setMap(this._map);
    };

    RidePage.prototype.addCity = function(cityName, cityTitle, slug, description, latitude, longitude) {
        this._city = new CityEntity(cityName, cityTitle, slug, description, latitude, longitude);

        this._city.addToContainer(this._cityContainer);
    };

    RidePage.prototype.addRide = function(title, description, latitude, longitude, location, date, time, weatherForecast) {
        this._ride = new RideEntity(title, description, latitude, longitude, location, date, time, weatherForecast);

        this._ride.addToContainer(this._rideContainer);
    };

    RidePage.prototype.addSubride = function(subrideId, title, description, latitude, longitude, location, date, time) {
        var subride = new SubrideEntity(subrideId, title, description, latitude, longitude, location, date, time);

        subride.addToContainer(this._subrideContainer, subrideId);
    };

    RidePage.prototype._initSubrideEvents = function() {
        var that = this;

        $('.subride a.subride-link').on('click', function() {
            var subrideId = $(this).data('subride-id');

            that._panMapToSubride(subrideId);
        });
    };

    RidePage.prototype._panMapToSubride = function(subrideId) {
        var subride = this._subrideContainer.getEntity(subrideId);

        var latLng = subride.getLatLng();

        this._map.setView(latLng, 14);
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
            that._photoViewModal.showPhoto(entityId);
        });
    };

    RidePage.prototype.focus = function() {
        if (!this._trackContainer.isEmpty()) {
            var bounds = this._trackContainer.getBounds();

            this._map.fitBounds(bounds);
        } else if (this._ride.hasLocation()) {
            this._map.setView([this._ride.getLatitude(), this._ride.getLongitude()], 10);
        } else {
            this._map.setView([this._city.getLatitude(), this._city.getLongitude()], 10);
        }
    };

    return RidePage;
});
