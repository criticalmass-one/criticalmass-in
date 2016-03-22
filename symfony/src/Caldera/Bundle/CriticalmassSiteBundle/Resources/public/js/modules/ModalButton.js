define([], function() {
    ModalButton = function() {
    };

    ModalButton.prototype._caption = '';
    ModalButton.prototype._icon = '';
    ModalButton.prototype._link = '';
    ModalButton.prototype._type = '';
    ModalButton.prototype._$button = null;

    ModalButton.prototype.setCaption = function(caption) {
        this._caption = caption;
    };

    ModalButton.prototype.setIcon = function(icon) {
        this._icon = icon;
    };

    ModalButton.prototype.setLink = function(link) {
        this._link = link;
    };

    ModalButton.prototype.setType = function(type) {
        this._type = type;
    };

    ModalButton.prototype._build = function() {
        this._$button = $([
            '<div class="modal fade" tabindex="-1" role="dialog">',
            '  <div class="modal-dialog modal-' + this._size + '">',
            '    <div class="modal-content">',
            '      <div class="modal-header">',
            '        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>',
            '        <h4 class="modal-title">',
            '          ' + this._modalTitle,
            '        </h4>',
            '      </div>',
            '      <div class="modal-body">',
            '        ' + this._modalBody,
            '      </div>',
            '      <div class="modal-footer">',
            '        ' + this._modalFooter,
            '      </div>',
            '    </div>',
            '  </div>',
            '</div>'
        ].join("\n"));
    };

    return ModalButton;
});