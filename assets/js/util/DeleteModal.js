import Handlebars from 'handlebars/dist/handlebars';

export default class DeleteModal {
    constructor(buttonElement) {
        const that = this;

        buttonElement.addEventListener('click', (event) => {
            event.preventDefault();

            const button = event.target;

            const href = button.getAttribute('href');

            const modalData = {
                title: button.dataset.deleteModalTitle,
                text: button.dataset.deleteModalText,
                size: button.dataset.deleteModalSize || 'md'
            }

            const source = this.getBootstrap5ModalTemplate();
            const template = Handlebars.compile(source);
            const modalHtml = template(modalData);

            const body = document.querySelector('body');
            body.insertAdjacentHTML('beforeend', modalHtml);

            const modalElement = document.getElementById('delete-modal');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            modalElement.addEventListener('hidden.bs.modal', () => {
                modalElement.remove();
            });

            const modalDeleteButton = document.querySelector('#delete-modal .btn-danger');
            modalDeleteButton.addEventListener('click', (event) => {
                event.preventDefault();

                that.performDeleteRequest(href);
            });
        });
    }

    performDeleteRequest(url) {
        const request = new XMLHttpRequest();
        request.open('DELETE', url, true);
        request.onreadystatechange = function () {
            if (this.status === this.DONE && this.readyState === 4) {
                location.reload();
            }

        }

        request.send();
    }

    getBootstrap5ModalTemplate() {
        return '<div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="delete-modal-label" aria-hidden="true">\n' +
            '  <div class="modal-dialog modal-{{ size }}">\n' +
            '    <div class="modal-content">\n' +
            '      <div class="modal-header">\n' +
            '        <h5 class="modal-title" id="delete-modal-label">{{ title }}</h5>\n' +
            '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>\n' +
            '      </div>\n' +
            '      <div class="modal-body">{{{ text }}}</div>\n' +
            '      <div class="modal-footer">\n' +
            '        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>\n' +
            '        <button type="button" class="btn btn-danger">Löschen</button>\n' +
            '      </div>\n' +
            '    </div>\n' +
            '  </div>\n' +
            '</div>';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const buttonList = document.querySelectorAll('a.delete-protection, button.delete-protection');

    buttonList.forEach((buttonElement) => {
        new DeleteModal(buttonElement);
    });
});
