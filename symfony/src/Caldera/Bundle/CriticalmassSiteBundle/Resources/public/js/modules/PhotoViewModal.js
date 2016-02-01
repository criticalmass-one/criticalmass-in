define(['Container', 'PhotoEntity'], function() {

    PhotoViewModal = function(context, options) {
    };

    PhotoViewModal.prototype._map = null;
    PhotoViewModal.prototype._photoContainer = null;


    PhotoViewModal.prototype.setPhotoContainer = function(photoContainer) {
        this._photoContainer = photoContainer;
    };

    PhotoViewModal.prototype.setMap = function(map) {
        this._map = map;
    };

    PhotoViewModal.prototype.showPhoto = function(entityId) {
        var photo = this._photoContainer.getEntity(entityId);

        var $modal = $('#photo-view-modal');
        $modal.find('img').attr('src', photo.getFilename());
        $modal.find('img').attr('id', 'photo-' + entityId);

        if (!$modal.hasClass('in')) {
            $modal.modal();
        } else {
            this._panMapToPhotoLocation(photo);
        }

        this._updatePhotoViewNavigation(entityId);
    };

    PhotoViewModal.prototype._panMapToPhotoLocation = function(photo) {
        this._map.setView([photo.getLatitude(), photo.getLongitude()], 15);
    };

    PhotoViewModal.prototype._updatePhotoViewNavigation = function(entityId) {
        var $modal = $('#photo-view-modal');
        var $nextPhotoButton = $modal.find('li.next');
        var $previousPhotoButton = $modal.find('li.previous');

        var previousPhotoEntityId = this._photoContainer.getPreviousIndex(entityId);
        var nextPhotoEntityId = this._photoContainer.getNextIndex(entityId);

        var that = this;

        $('body').off('keydown').on('keydown', function(e) {
            if (nextPhotoEntityId && (e.keyCode || e.which) == 39) {
                that.showPhoto(nextPhotoEntityId);
            }

            if (previousPhotoEntityId && (e.keyCode || e.which) == 37) {
                that.showPhoto(previousPhotoEntityId);
            }
        });

        if (previousPhotoEntityId) {
            $previousPhotoButton.find('a').off('click').on('click', function (element) {
                element.preventDefault();
                that.showPhoto(previousPhotoEntityId);
            });

            $previousPhotoButton.removeClass('disabled');
        } else {
            $previousPhotoButton.addClass('disabled');
            $previousPhotoButton.find('a').off('click');
        }

        if (nextPhotoEntityId) {
            $nextPhotoButton.find('a').off('click').on('click', function(element) {
                element.preventDefault();

                that.showPhoto(nextPhotoEntityId);
            });

            $nextPhotoButton.removeClass('disabled');
        } else {
            $nextPhotoButton.addClass('disabled');
            $nextPhotoButton.find('a').off('click');
        }
    };

    return PhotoViewModal;
});
