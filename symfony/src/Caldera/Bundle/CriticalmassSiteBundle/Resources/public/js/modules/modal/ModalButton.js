define([], function() {
    ModalButton = function() {
    };

    ModalButton.prototype._$button = null;
    ModalButton.prototype._html = null;

    ModalButton.prototype._icon = null;
    ModalButton.prototype._caption = null;
    ModalButton.prototype._class = null;
    ModalButton.prototype._href = null;

    ModalButton.prototype._onClickEventCallback = null;

    ModalButton.prototype.setCaption = function(caption) {
        this._caption = caption;
    };

    ModalButton.prototype.setHref = function(href) {
        this._href = href;
    };

    ModalButton.prototype.setClass = function(clas) {
        this._class = clas;
    };

    ModalButton.prototype.setOnClickEvent = function(callback) {
        this._onClickEventCallback = callback;
    };

    ModalButton.prototype.render = function() {
        if (this._href) {
            this._html = '<a class="btn ' + this._class + '" href="' + this._href + '">' + this._caption + '</a>';
        } else {
            this._html = '<button class="btn ' + this._class + '">' + this._caption + '</button>';
        }

        this._$button = $(this._html);

        this._$button.on('click', this._onClickEventCallback);

        return this._$button;
    };

    return ModalButton;
});