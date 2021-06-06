define(['CriticalService', 'dateformat', 'leaflet', 'MarkerEntity', 'leaflet.extra-markers'], function (CriticalService, dateFormat) {
    SubrideEntity = function () {
    };

    SubrideEntity.prototype = new MarkerEntity();
    SubrideEntity.prototype.constructor = SubrideEntity;

    SubrideEntity.prototype._id = null;
    SubrideEntity.prototype._title = null;
    SubrideEntity.prototype._description = null;
    SubrideEntity.prototype._location = null;
    SubrideEntity.prototype._timestamp = null;
    SubrideEntity.prototype._weather = null;

    SubrideEntity.prototype._CriticalService = CriticalService;

    SubrideEntity.prototype._initIcon = function () {
        this._icon = L.ExtraMarkers.icon({
            icon: 'fa-circle',
            markerColor: 'green',
            shape: 'circle',
            prefix: 'far'
        });
    };

    SubrideEntity.prototype._setupModalContent = function () {
        this._modal.setTitle(this._title);

        var content = '<dl class="dl-horizontal">';
        content += '<dt>Datum:</dt><dd>' + dateFormat(this._timestamp, 'dd.mm.yyyy') + '</dd>';
        content += '<dt>Uhrzeit:</dt><dd>' + dateFormat(this._timestamp, 'HH:MM') + '&nbsp;Uhr</dd>';
        content += '<dt>Treffpunkt:</dt><dd>' + this._location + '</dd>';

        if (this._weather) {
            content += '<dt>Wetter:</dt><dd>' + this._weather + '</dd>';
        }

        content += '</dl>';

        if (this._description) {
            content += '<p>' + this._description + '</p>';
        }

        this._modal.setBody(content);

        var that = this;

        var centerButton = new ModalButton();
        centerButton.setCaption('Zentrieren');
        centerButton.setIcon('map-pin');
        centerButton.setClass('btn-success');
        centerButton.setOnClickEvent(function () {
            that._CriticalService.getMap().setView([that._latitude, that._longitude], 15);
        });

        var closeButton = new CloseModalButton;

        var buttons = [
            centerButton,
            closeButton
        ];

        this._modal.setButtons(buttons);
    };

    return SubrideEntity;
});
