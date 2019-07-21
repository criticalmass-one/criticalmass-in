define(['CriticalService', 'dateformat', 'leaflet', 'MarkerEntity', 'leaflet.extra-markers', 'ModalButton', 'CloseModalButton'], function (CriticalService, dateFormat) {
    RideEntity = function () {
    };

    RideEntity.prototype = new MarkerEntity();
    RideEntity.prototype.constructor = RideEntity;

    RideEntity.prototype._CriticalService = CriticalService;
    RideEntity.prototype._title = null;
    RideEntity.prototype._description = null;
    RideEntity.prototype._citySlug = null;
    RideEntity.prototype._location = null;
    RideEntity.prototype._dateTime = null;

    RideEntity.prototype._initIcon = function () {
        this._icon = L.ExtraMarkers.icon({
            icon: 'fa-bicycle',
            markerColor: 'red',
            shape: 'round',
            prefix: 'fa'
        });
    };

    RideEntity.prototype._setupModalContent = function () {
        this._modal.setTitle(this._title);

        var content = '<dl class="dl-horizontal">';

        content += '<dt>Datum:</dt><dd>' + dateFormat(this._dateTime, 'dd.mm.yyyy') + '</dd>';

        if (this._hasTime) {
            content += '<dt>Uhrzeit:</dt><dd>' + dateFormat(this._dateTime, 'HH:MM') + ' Uhr</dd>';
        } else {
            content += '<dt>Uhrzeit:</dt><dd>die Uhrzeit ist noch nicht bekannt</dd>';
        }

        if (this._location && this._latitude && this._longitude) {
            content += '<dt>Treffpunkt:</dt><dd>' + this._location + '</dd>';
        } else {
            content += '<dt>Treffpunkt:</dt><dd>der Treffpunkt ist noch nicht bekannt</dd>';
        }

        if (this._weather) {
            content += '<dt>Wetter:</dt><dd>' + this._weather + '</dd>';
        }

        content += '</dl>';

        if (this._description) {
            content += '<p>' + this._description + '</p>';
        }

        this._modal.setBody(content);

        this._setupModalButtons();
    };

    RideEntity.prototype._setupModalButtons = function () {
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
        cityButton.setHref(Routing.generate('caldera_criticalmass_city_show', {citySlug: this._city._slug}));

        var rideButton = new ModalButton();
        rideButton.setCaption('Tourseite');
        rideButton.setIcon('bicycle');
        rideButton.setClass('btn-success');
        rideButton.setHref(Routing.generate('caldera_criticalmass_ride_show', {
            citySlug: this._city._slug,
            rideDate: dateFormat(this._dateTime, 'yyyy-mm-dd')
        }));

        var closeButton = new CloseModalButton;

        var buttons = [
            cityButton,
            rideButton,
            centerButton,
            closeButton
        ];

        this._modal.setButtons(buttons);
    };

    RideEntity.prototype.getDate = function () {
        return this._date;
    };

    RideEntity.prototype.getLocation = function () {
        return this._location;
    };

    return RideEntity;
});
