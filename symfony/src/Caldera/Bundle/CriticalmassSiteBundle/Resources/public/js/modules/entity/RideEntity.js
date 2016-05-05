define(['leaflet', 'MarkerEntity', 'leaflet-extramarkers', 'ModalButton', 'CloseModalButton', 'dateformat'], function() {
    RideEntity = function () {
    };

    RideEntity.prototype = new MarkerEntity();
    RideEntity.prototype.constructor = RideEntity;

    RideEntity.prototype._title = null;
    RideEntity.prototype._description = null;
    RideEntity.prototype._citySlug = null;
    RideEntity.prototype._location = null;
    RideEntity.prototype._timestamp = null;

    RideEntity.prototype._initIcon = function() {
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

        content += '<dt>Datum:</dt><dd>' + this._timestamp.format('dd.mm.yyyy') + '</dd>';
        content += '<dt>Uhrzeit:</dt><dd>' + this._timestamp.format('HH:MM') + ' Uhr</dd>';
        content += '<dt>Treffpunkt:</dt><dd>' + this._location + '</dd>';

        if (this._weather) {
            content += '<dt>Wetter:</dt><dd>' + this._weather + '</dd>';
        }

        content += '</dl>';

        if (this._description) {
            content += '<p>' + this._description + '</p>';
        }

        this._modal.setBody(content);
        
        var cityButton = new ModalButton();
        cityButton.setCaption('Städteseite');
        cityButton.setIcon('university');
        cityButton.setClass('btn-success');
        //cityButton.setHref(Routing.generate('caldera_criticalmass_desktop_city_show', { citySlug: this._citySlug }));

        var buttons = [
            cityButton,
            new CloseModalButton()
        ];

        this._modal.setButtons(buttons);
    };

    RideEntity.prototype.getDate = function() {
        return this._date;
    };

    return RideEntity;
});