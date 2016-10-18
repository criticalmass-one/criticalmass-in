define(['CriticalService', 'leaflet', 'MarkerEntity', 'leaflet-extramarkers', 'ModalButton', 'CloseModalButton', 'dateformat'], function (CriticalService) {
    EventEntity = function () {
    };

    EventEntity.prototype = new MarkerEntity();
    EventEntity.prototype.constructor = EventEntity;

    EventEntity.prototype._CriticalService = CriticalService;
    EventEntity.prototype._title = null;
    EventEntity.prototype._description = null;
    EventEntity.prototype._slug = null;
    EventEntity.prototype._title = null;
    EventEntity.prototype._location = null;
    EventEntity.prototype._timestamp = null;

    EventEntity.prototype._initIcon = function () {
        this._icon = L.ExtraMarkers.icon({
            icon: 'fa-calendar',
            markerColor: 'white',
            shape: 'round',
            prefix: 'fa'
        });
    };

    EventEntity.prototype._setupModalContent = function () {
        this._modal.setTitle(this._title);

        var content = '<dl class="dl-horizontal">';

        content += '<dt>Datum:</dt><dd>' + this._timestamp.format('dd.mm.yyyy') + '</dd>';

        if (this._hasTime) {
            content += '<dt>Uhrzeit:</dt><dd>' + this._timestamp.format('HH:MM') + ' Uhr</dd>';
        } else {
            content += '<dt>Uhrzeit:</dt><dd>die Uhrzeit ist noch nicht bekannt</dd>';
        }

        if (this._hasLocation && this._location) {
            content += '<dt>Ort:</dt><dd>' + this._location + '</dd>';
        } else {
            content += '<dt>Ort:</dt><dd>der Ort ist noch nicht bekannt</dd>';
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

    EventEntity.prototype._setupModalButtons = function () {
        var that = this;

        var centerButton = new ModalButton();
        centerButton.setCaption('Zentrieren');
        centerButton.setIcon('map-pin');
        centerButton.setClass('btn-success');
        centerButton.setOnClickEvent(function () {
            that._CriticalService.getMap().setView([that._latitude, that._longitude], 13);
        });

        var eventButton = new ModalButton();
        eventButton.setCaption('Eventseite');
        eventButton.setIcon('calendar');
        eventButton.setClass('btn-success');
        eventButton.setHref(Routing.generate('caldera_criticalmass_event_show', {
            eventSlug: this._slug,
            citySlug: this._city._slug
        }));

        var closeButton = new CloseModalButton;

        var buttons = [
            eventButton,
            centerButton,
            closeButton
        ];

        this._modal.setButtons(buttons);
    };

    EventEntity.prototype.getDate = function () {
        return this._date;
    };

    return EventEntity;
});