import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';
import Handlebars from 'handlebars/dist/handlebars';

export default class extends Controller {
    static values = {
        title: String,
        text: String,
        size: { type: String, default: 'md' }
    }

    open(event) {
        event.preventDefault();

        const href = this.element.getAttribute('href');

        const modalData = {
            title: this.titleValue,
            text: this.textValue,
            size: this.sizeValue
        };

        const source = this.getModalTemplate();
        const template = Handlebars.compile(source);
        const modalHtml = template(modalData);

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        const modalElement = document.getElementById('delete-modal');
        const modal = new Modal(modalElement);
        modal.show();

        modalElement.addEventListener('hidden.bs.modal', () => {
            modal.dispose();
            modalElement.remove();
        }, { once: true });

        const deleteButton = modalElement.querySelector('.btn-danger');
        deleteButton.addEventListener('click', (e) => {
            e.preventDefault();
            this.performDeleteRequest(href);
        }, { once: true });
    }

    performDeleteRequest(url) {
        fetch(url, { method: 'DELETE' })
            .then(response => {
                if (response.ok) {
                    location.reload();
                }
            });
    }

    getModalTemplate() {
        return '' +
            '<div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="delete-modal-label" aria-hidden="true">' +
            '  <div class="modal-dialog modal-{{ size }}">' +
            '    <div class="modal-content">' +
            '      <div class="modal-header">' +
            '        <h5 class="modal-title" id="delete-modal-label">{{ title }}</h5>' +
            '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
            '      </div>' +
            '      <div class="modal-body">{{{ text }}}</div>' +
            '      <div class="modal-footer">' +
            '        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>' +
            '        <button type="button" class="btn btn-danger">Löschen</button>' +
            '      </div>' +
            '    </div>' +
            '  </div>' +
            '</div>';
    }
}
