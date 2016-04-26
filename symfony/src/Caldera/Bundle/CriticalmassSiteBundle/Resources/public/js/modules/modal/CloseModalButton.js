define([], function() {
    CloseModalButton = function() {
    };

    CloseModalButton.prototype._$button = null;
    CloseModalButton.prototype._html = null;

    CloseModalButton.prototype.render = function() {
        this._html = '<button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>';

        this._$button = $(this._html);

        return this._html;
    };

    return CloseModalButton;
});