define(['Container', 'PhotoEntity', 'Modal'], function() {

    PhotoViewModal = function(context, options) {
        this._options = options;
    };

    PhotoViewModal.prototype._map = null;
    PhotoViewModal.prototype._photoContainer = null;
    PhotoViewModal.prototype._options = null;
    PhotoViewModal.prototype._modal = null;

    PhotoViewModal.prototype.setPhotoContainer = function(photoContainer) {
        this._photoContainer = photoContainer;
    };

    PhotoViewModal.prototype.setMap = function(map) {
        this._map = map;
    };

    PhotoViewModal.prototype.showPhoto = function(entityId) {
        var photo = this._photoContainer.getEntity(entityId);

        this._modal = new Modal();
        this._modal.setTitle('Foto anzeigen');
        this._modal.setBody('<img id="photo-' + entityId + '" src="' + photo.getFilename() + '" class="img-responsive" />');
        this._modal.setFooter('<nav><ul class="pager no-margin-top no-margin-bottom"><li class="previous"><a href="#"><span aria-hidden="true">&larr;</span> Voriges Foto</a></li><li class="next"><a href="#">Nächstes Foto <span aria-hidden="true">&rarr;</span></a></li></ul></nav>');
        this._modal.setSize('lg');

        if (!this._modal.isVisible()) {
            this._modal.show();
        } else {
            this._panMapToPhotoLocation(photo);
        }

        this._updatePhotoViewNavigation(entityId);

        this._countView(photo);
    };

    PhotoViewModal.prototype._countView = function(photo) {
        var countUrl = this._options.photoCounterUrl + '?photoId=' + photo.getId();

        $.get(countUrl);
    };

    PhotoViewModal.prototype._panMapToPhotoLocation = function(photo) {
        this._map.setView([photo.getLatitude(), photo.getLongitude()], 15);
    };

    PhotoViewModal.prototype._updatePhotoViewNavigation = function(entityId) {
        var $modal = $('#photo-view-modal');
        var $nextPhotoButton = $modal.find('li.next');
        var $previousPhotoButton = $modal.find('li.previous');

        var photo = this._photoContainer.getEntity(entityId);
        var $imageLink = $modal.find('a#photo-view-page-link');
        var photoViewPageUrl = this._options.photoViewPageUrl.replace('photoId', photo.getId());

        var previousPhotoEntityId = this._photoContainer.getPreviousIndex(entityId);
        var nextPhotoEntityId = this._photoContainer.getNextIndex(entityId);

        var that = this;

        $imageLink.attr('href', photoViewPageUrl);

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
