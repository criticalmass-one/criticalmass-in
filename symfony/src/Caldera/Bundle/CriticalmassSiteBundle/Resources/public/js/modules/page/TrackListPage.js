define(['AutoMap', 'leaflet-polyline', 'Modal', 'ModalButton', 'CloseModalButton'], function() {
    TrackListPage = function (settings) {
        this._init();
    };

    TrackListPage.prototype._init = function() {
        var that = this;

        $('.preview-map').each(function(index, value) {
            var map = new AutoMap($(value).attr('id'));
        });

        $('a.delete-track').on('click', function(element) {
            that._showDeleteModal($(this).data('track-id'));
        });

        this._modal = new Modal();
        this._modal.setSize('xs');
    };

    TrackListPage.prototype._showDeleteModal = function(trackId) {
        this._modal.setTitle('Track löschen');

        this._modal.setBody('Willst du diesen Track wirklich löschen?');

        var deleteButton = new ModalButton();
        deleteButton.setCaption('Löschen');
        deleteButton.setIcon('cross');
        deleteButton.setClass('btn-success');
        deleteButton.setHref(Routing.generate('caldera_criticalmass_track_delete', { trackId: trackId }));

        var closeButton = new CloseModalButton;

        var buttons = [
            deleteButton,
            closeButton
        ];

        this._modal.setButtons(buttons);

        this._modal.show();
    };
    
    return TrackListPage;
});