define(['CriticalService', 'Map', 'Container', 'ClusterContainer', 'CityEntity', 'RideEntity', 'TimelapseTrackEntity', 'SubrideEntity', 'MapLayerControl', 'PhotoEntity', 'PhotoViewModal', 'Timelapse'], function(CriticalService) {

    RidePage = function(context, options) {
        this.options = options;

        this._initMap();
        this._initContainers();
        this._initLayers();
        this._initLayerControl();
        this._initTrackToggleEvent();
        this._initPhotoViewModal();
        this._initSubrideEvents();
        this._initTimelapse();

        this._CriticalService = CriticalService;
    };

    RidePage.prototype._map = null;
    RidePage.prototype._ride = null;
    RidePage.prototype._city = null;
    RidePage.prototype._subrideContainer = null;
    RidePage.prototype._layerControl = null;
    RidePage.prototype._layers = null;
    RidePage.prototype._timelapse = null;
    RidePage.prototype._CriticalService = CriticalService;

    RidePage.prototype.init = function() {

    };

    RidePage.prototype._initTimelapse = function() {
        var $trackPanel = $('#tracks');

        if ($trackPanel.length > 0) {
            this._timelapse = new Timelapse(this);
            this._timelapse.init();

            this._timelapse.setInitCallbackFunction(this._finishTimelapseInit);
            this._timelapse.setLoadCallbackFunction(this._timelapseTrackLoadCallback);

            var that = this;

            $('button#timelapse-start-button').on('click', function() {
                that._startTimelapseInit();
            });
        }
    };

    RidePage.prototype._startTimelapseInit = function() {
        $('#timelapse-start').hide();
        $('#timelapse-loader').show();
        $('#timelapse-track-total').html(this._trackContainer.countEntities());

        this._timelapse.startInit();
    };

    RidePage.prototype._finishTimelapseInit = function() {
        $('#timelapse-loader').hide();
        $('#timelapse-control').show();
    };

    RidePage.prototype._timelapseTrackLoadCallback = function(counter) {
        $('#timelapse-track-number').html(counter);
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
        this._photoContainer = new ClusterContainer();
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
            photoViewPageUrl: this.options.photoViewPageUrl,
            photoCounterUrl: this.options.photoCounterUrl
        };

        this._photoViewModal = new PhotoViewModal(null, options);
        this._photoViewModal.setPhotoContainer(this._photoContainer);
        this._photoViewModal.setMap(this._map);
    };

    RidePage.prototype.addCity = function(cityName, cityTitle, slug, description, latitude, longitude) {
        this._city = new CityEntity(cityName, cityTitle, slug, description, latitude, longitude);

        this._city.addToContainer(this._cityContainer);
    };

    RidePage.prototype.addRide = function(rideJson) {
        this._ride = this._CriticalService.factory.createRide(rideJson);

        this._ride.addToContainer(this._rideContainer);
    };

    RidePage.prototype.addSubride = function(subrideJson) {
        var subride = this._CriticalService.factory.createSubride(subrideJson);

        subride.addToContainer(this._subrideContainer, subride.getId());
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

    RidePage.prototype.addTrack = function(trackId, polyline, colorRed, colorGreen, colorBlue, username, backgroundImage) {
        var track = new TimelapseTrackEntity(trackId, polyline, colorRed, colorGreen, colorBlue, username, backgroundImage);

        track.addToContainer(this._trackContainer, trackId);
    };

    RidePage.prototype._toggleTrack = function(trackId) {
        this._trackContainer.toggleIndexEntityInLayer(trackId);
    };

    RidePage.prototype.addPhoto = function(photoJson, filename) {
        var photo = this._CriticalService.factory.createPhoto(photoJson);

        photo.setFilename(filename);

        photo.addToContainer(this._photoContainer, photo.getId());

        var that = this;

        photo.on('click', function() {
            that._photoViewModal.showPhoto(photo.getId());
        });
    };

    RidePage.prototype.focus = function() {
        if (!this._trackContainer.isEmpty()) {
            var bounds = this._trackContainer.getBounds();

            this._map.fitBounds(bounds);
        } else if (this._ride && this._ride.hasLocation()) {
            this._map.setView([this._ride.getLatitude(), this._ride.getLongitude()], 10);
        } else {
            this._map.setView([this._city.getLatitude(), this._city.getLongitude()], 10);
        }
    };

    return RidePage;
});
