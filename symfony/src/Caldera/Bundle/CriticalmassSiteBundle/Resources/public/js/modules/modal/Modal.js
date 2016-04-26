define([], function() {

    Modal = function() {
    };

    Modal.prototype.$modal = null;
    Modal.prototype._modalTitle = '';
    Modal.prototype._modalBody = '';
    Modal.prototype._modalFooter = '';
    Modal.prototype._size = 'lg';
    Modal.prototype._buttonList = [];

    Modal.prototype.setSize = function(size) {
        this._size = size;
    };

    Modal.prototype.setTitle = function(title) {
        this._modalTitle = title;

        if (this.$modal) {
            this.$modal.find('.modal-header h4.modal-title').html(title);
        }
    };

    Modal.prototype.setBody = function(body) {
        this._modalBody = body;

        if (this.$modal) {
            this.$modal.find('.modal-body').html(body);
        }
    };

    Modal.prototype.setFooter = function(footer) {
        this._modalFooter = footer;

        if (this.$modal) {
            this.$modal.find('.modal-footer').html(footer);
        }
    };

    Modal.prototype.addButton = function(button) {
        this._buttonList.push(button);
    };

    Modal.prototype.resetButtons = function() {
        this._buttonList = [];
    };

    Modal.prototype._renderButtons = function() {
        var $btnGroup = $('<div class="btn-group">');

        $.each(this._buttonList, function(index, button) {
            $btnGroup.prepend(button.render());
        });

        this.$modal.find('.modal-footer').append($btnGroup);
    };

    Modal.prototype.show = function() {
        this._buildHtml();

        if (this._buttonList.length > 0) {
            this._renderButtons();
        }

        this._inject();
        this.$modal.modal();

        this._installDestroyEvent();
    };

    Modal.prototype.isVisible = function() {
        if (!this.$modal) {
            return false;
        }

        return this.$modal.hasClass('in');
    };

    Modal.prototype._installDestroyEvent = function() {
        var that = this;

        this.$modal.on('hidden.bs.modal', function() {
            that.$modal.remove();
            that.$modal = null;
        });
    };

    Modal.prototype._buildHtml = function() {
        this.$modal = $([
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