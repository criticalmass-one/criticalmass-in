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

    ModalButton.prototype.setClass = function(btnClass) {
        this._class = btnClass;
    };

    ModalButton.prototype.setIcon = function(icon) {
        this._icon = icon;
    };

    ModalButton.prototype.setOnClickEvent = function(callback) {
        this._onClickEventCallback = callback;
    };

    ModalButton.prototype.render = function() {
        var icon = '';

        if (this._icon) {
            icon = '<i class="fa fa-' + this._icon + '" aria-hidden="true"></i> ';
        }

        if (this._href) {
            this._html = '<a class="btn ' + this._class + '" href="' + this._href + '">' + icon + this._caption + '</a>';
        } else {
            this._html = '<button class="btn ' + this._class + '">' + icon + this._caption + '</button>';
        }

        this._$button = $(this._html);

        this._$button.on('click', this._onClickEventCallback);

        return this._$button;
    };

    return ModalButton;
});