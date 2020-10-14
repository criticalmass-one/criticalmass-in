define(['jquery', 'Modal'], function ($) {

    HintModal = function (context, options) {
        this._options = options;

        $(context).click(this.showModal.bind(this));
    };

    HintModal.prototype._modal = null;

    HintModal.prototype.showModal = function(event) {
        var $target = $(event.target);

        var title = $target.data('modal-hint-title');
        var text = $target.data('modal-hint-text');
        var size = $target.data('modal-hint-size') || 'md';

        // was the modal already created?
        if (this._modal) {
            // if yes, just update the modal content and change the currently shown image
            this._updateModal(title, text, size);
        } else {
            // if not, create the modal now
            this._createModal(title, text, size);
        }

        // next question: is the modal visible?
        if (!this._modal.isVisible()) {
            // if not, show it now
            this._modal.show();
        }
    };

    HintModal.prototype._updateModal = function (title, text, size) {
        this._modal.setTitle(title);
        this._modal.setBody(text);
    };

    HintModal.prototype._createModal = function (title, text, size) {
        this._modal = new Modal();
        this._modal.setTitle(title);
        this._modal.setBody(text);

        this._modal.setFooter('<a href="/login"  class="btn btn-primary">zum Login</a>');

        this._modal.setSize(size);
    };

    return HintModal;
});
