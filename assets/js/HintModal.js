import Handlebars from 'handlebars/dist/handlebars';

export default class HintModal {
    constructor(element, options) {
        element.addEventListener('click', (event) => {
            const target = event.target;

            const hintData = {
                title: target.dataset.modalHintTitle,
                text: target.dataset.modalHintText,
                size: target.dataset.modalHintSize || 'md'
            }

            const source = this.getBootstrap5ModalTemplate();
            const template = Handlebars.compile(source);
            const modalHtml = template(hintData);

            const body = document.querySelector('body');
            body.insertAdjacentHTML('beforeend', modalHtml);

            const modalElement = document.getElementById('hint-modal');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            modalElement.addEventListener('hidden.bs.modal', () => {
                modalElement.remove();
            });
        });
    }

    getBootstrap5ModalTemplate() {
        return '<div class="modal fade" id="hint-modal" tabindex="-1" aria-labelledby="hint-modal-label" aria-hidden="true">\n' +
            '  <div class="modal-dialog modal-{{ size }}">\n' +
            '    <div class="modal-content">\n' +
            '      <div class="modal-header">\n' +
            '        <h5 class="modal-title" id="hint-modal-label">{{ title }}</h5>\n' +
            '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>\n' +
            '      </div>\n' +
            '      <div class="modal-body">{{{ text }}}</div>\n' +
            '      <div class="modal-footer">\n' +
            '        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>\n' +
            '      </div>\n' +
            '    </div>\n' +
            '  </div>\n' +
            '</div>';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const elementList = document.querySelectorAll('.modal-hint');

    elementList.forEach((element) => {
        new HintModal(element);
    });
});
