define([], function() {
    BaseModalButton = function($modal) {
        this._$modal = $modal;
    };

    Modal.prototype._$modal = null;
    Modal.prototype._$button = null;

    Modal.prototype._buildHtml = function() {
        this._$button = $([
            '<div id="criticalmass-modal" class="modal fade" tabindex="-1" role="dialog">',
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

    Modal.prototype._inject = function() {
        $('body').append(this.$modal);
    };

    return Modal;
});