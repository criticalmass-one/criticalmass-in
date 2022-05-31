define(['CriticalService', 'leaflet', 'MarkerEntity', 'ModalButton', 'CloseModalButton', 'leaflet.extra-markers'], function (CriticalService) {
    CityEntity = function () {
    };

    CityEntity.prototype = new MarkerEntity();
    CityEntity.prototype.constructor = CityEntity;

    CityEntity.prototype._CriticalService = CriticalService;
    CityEntity.prototype._title = null;
    CityEntity.prototype._name = null;
    CityEntity.prototype._slug = null;
    CityEntity.prototype._description = null;

    CityEntity.prototype._initIcon = function () {
        this._icon = L.ExtraMarkers.icon({
            icon: 'fa-university',
            markerColor: 'blue',
            shape: 'round',
            prefix: 'far'
        });
    };

    CityEntity.prototype._setupModalContent = function () {
        this._modal.setTitle(this._title);

        if (this._description) {
            this._modal.setBody(this._description);
        } else {
            this._modal.setBody('<div class="alert alert-info" role="alert"><strong>Schade!</strong> Zu dieser Stadt wurden bislang keine weiteren Informationen hinterlegt.</div>');
        }

        var that = this;

        var centerButton = new ModalButton();
        centerButton.setCaption('Zentrieren');
        centerButton.setIcon('map-pin');
        centerButton.setClass('btn-success');
        centerButton.setOnClickEvent(function () {
            that._CriticalService.getMap().setView([that._latitude, that._longitude], 13);
        });

        var cityButton = new ModalButton();
        cityButton.setCaption('Städteseite');
        cityButton.setIcon('university');
        cityButton.setClass('btn-success');
        cityButton.setHref(Routing.generate('caldera_criticalmass_city_show', {citySlug: this._slug}));

        var closeButton = new CloseModalButton;

        var buttons = [
            cityButton,
            centerButton,
            closeButton
        ];

        this._modal.setButtons(buttons);
    };

    CityEntity.prototype.getSlug = function () {
        return this._slug;
    };

    return CityEntity;
});
