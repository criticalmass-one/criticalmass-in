import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';
import Handlebars from 'handlebars/dist/handlebars';

export default class extends Controller {
    static values = {
        hintTitle: String,
        hintText: String,
        hintSize: { type: String, default: 'md' }
    }

    connect() {
        this.openHandler = this.open.bind(this);
        this.element.addEventListener('click', this.openHandler);
    }

    disconnect() {
        this.element.removeEventListener('click', this.openHandler);
    }

    open(event) {
        event.preventDefault();

        const target = event.currentTarget;

        const hintData = {
            title: target.dataset.modalHintTitle,
            text: target.dataset.modalHintText,
            size: target.dataset.modalHintSize || 'md'
        };

        const source = this.getModalTemplate();
        const template = Handlebars.compile(source);
        const modalHtml = template(hintData);

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        const modalElement = document.getElementById('hint-modal');
        const modal = new Modal(modalElement);
        modal.show();

        modalElement.addEventListener('hidden.bs.modal', () => {
            modal.dispose();
            modalElement.remove();
        }, { once: true });
    }

    getModalTemplate() {
        return '' +
            '<div class="modal fade" id="hint-modal" tabindex="-1" aria-labelledby="hint-modal-label" aria-hidden="true">' +
            '  <div class="modal-dialog modal-{{ size }}">' +
            '    <div class="modal-content">' +
            '      <div class="modal-header">' +
            '        <h5 class="modal-title" id="hint-modal-label">{{ title }}</h5>' +
            '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
            '      </div>' +
            '      <div class="modal-body">{{{ text }}}</div>' +
            '      <div class="modal-footer">' +
            '        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>' +
            '      </div>' +
            '    </div>' +
            '  </div>' +
            '</div>';
    }
}
