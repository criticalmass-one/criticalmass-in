define(['CriticalService', 'RideEntity'], function (CriticalService) {
    LiveRideEntity = function () {
    };

    LiveRideEntity.prototype = new RideEntity();
    LiveRideEntity.prototype.constructor = LiveRideEntity;

    LiveRideEntity.prototype._CriticalService = CriticalService;

    LiveRideEntity.prototype._setupModalButtons = function () {
        var that = this;

        var buttons = [];

        var locationButton = new ModalButton();
        locationButton.setCaption('Treffpunkt');
        locationButton.setIcon('map-pin');
        locationButton.setClass('btn-success');
        locationButton.setOnClickEvent(function () {
            that._CriticalService.getMap().setView([that._latitude, that._longitude], 13);
        });

        buttons.push(locationButton);

        if (this._CriticalService.getMapPositions().countPositions > 0) {
            var latestPositionButton = new ModalButton();
            latestPositionButton.setCaption('Neuste Position');
            latestPositionButton.setIcon('bicycle');
            latestPositionButton.setClass('btn-success');
            latestPositionButton.setOnClickEvent(function () {
                var latLng = that._CriticalService.getMapPositions().getLatestLatLng();

                if (latLng) {
                    that._CriticalService.getMap().setView(latLng, 13);
                }
            });

            buttons.push(latestPositionButton);

            var allPositionButton = new ModalButton();
            allPositionButton.setCaption('Alle Teilnehmer');
            allPositionButton.setIcon('bicycle');
            allPositionButton.setClass('btn-success');
            allPositionButton.setOnClickEvent(function () {
                var bounds = that._CriticalService.getMapPositions().getBounds();

                if (bounds) {
                    that._CriticalService.getMap().fitBounds();
                }
            });

            buttons.push(allPositionButton);
        }

        var closeButton = new CloseModalButton;

        buttons.push(closeButton);

        this._modal.setButtons(buttons);
    };

    return LiveRideEntity;
});