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

            const source = this.getBootstrap3ModalTemplate();
            const template = Handlebars.compile(source);
            const modalHtml = template(modalData);

            const body = document.querySelector('body');
            body.insertAdjacentHTML('beforeend', modalHtml);

            const $modal = $('#delete-modal');
            $modal.modal('show');
            $modal.on('hidden.bs.modal', (event) => {
                document.getElementById('delete-modal').remove();
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

    getBootstrap3ModalTemplate() {
        return '<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="delete-modal-label">\n' +
            '  <div class="modal-dialog modal-{{ size }}" role="document">\n' +
            '    <div class="modal-content">\n' +
            '      <div class="modal-header">\n' +
            '        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n' +
            '        <h4 class="modal-title" id="delete-modal-label">{{ title }}</h4>\n' +
            '      </div>\n' +
            '      <div class="modal-body">{{{ text }}}</div>\n' +
            '      <div class="modal-footer">\n' +
            '        <button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>\n' +
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
