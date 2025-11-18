import { Controller } from '@hotwired/stimulus';
import Handlebars from 'handlebars/dist/handlebars';

export default class extends Controller {
    static targets = [];
    static values = {
        hintTitle: String,
        hintText: String,
        hintSize: { type: String, default: 'md' }
    }

    connect() {
        this.element.addEventListener('click', this.open.bind(this));
    }

    disconnect() {
        this.element.removeEventListener('click', this.open.bind(this));
    }

    open(event) {
        const target = event.currentTarget;

        const hintData = {
            title: target.dataset.modalHintTitle,
            text: target.dataset.modalHintText,
            size: target.dataset.modalHintSize || 'md'
        };

        const source = this.getBootstrap3ModalTemplate();
        const template = Handlebars.compile(source);
        const modalHtml = template(hintData);

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        const $modal = $('#hint-modal');
        $modal.modal('show');

        $modal.on('hidden.bs.modal', () => {
            document.getElementById('hint-modal').remove();
        });
    }

    getBootstrap3ModalTemplate() {
        return '' +
            '<div class="modal fade" id="hint-modal" tabindex="-1" role="dialog" aria-labelledby="hint-modal-label">' +
            '  <div class="modal-dialog modal-{{ size }}" role="document">' +
            '    <div class="modal-content">' +
            '      <div class="modal-header">' +
            '        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '        <h4 class="modal-title" id="hint-modal-label">{{ title }}</h4>' +
            '      </div>' +
            '      <div class="modal-body">{{{ text }}}</div>' +
            '      <div class="modal-footer">' +
            '        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>' +
            '      </div>' +
            '    </div>' +
            '  </div>' +
            '</div>';
    }
}
