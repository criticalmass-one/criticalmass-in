define([], function() {

    Modal = function(context, options) {
    };

    Modal.prototype._$modal = null;
    Modal.prototype._modalTitle = '';
    Modal.prototype._modalBody = '';
    Modal.prototype._modalFooter = '';
    Modal.prototype._size = 'lg';

    Modal.prototype.setSize = function(size) {
        this._size = size;
    };

    Modal.prototype.setTitle = function(title) {
        this._modalTitle = title;

        $('#criticalmass-modal .modal-header h4.modal-title').html(title);
    };

    Modal.prototype.setBody = function(body) {
        this._modalBody = body;

        $('#criticalmass-modal .modal-body').html(body);
    };

    Modal.prototype.setFooter = function(footer) {
        this._modalFooter = footer;

        $('#criticalmass-modal .modal-footer').html(footer);
    };

    Modal.prototype.show = function() {
        this._buildHtml();
        this._inject();
        this._$modal.modal();
    };

    Modal.prototype.isVisible = function() {
        if (!this._$modal) {
            return false;
        }

        return this._$modal.hasClass('in');
    };

    Modal.prototype._buildHtml = function() {
        this._$modal = $([
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
        $('body').append(this._$modal);
    };

    return Modal;
});