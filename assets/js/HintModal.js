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

            const source = this.getBootstrap3ModalTemplate();
            const template = Handlebars.compile(source);
            const modalHtml = template(hintData);

            const body = document.querySelector('body');
            body.insertAdjacentHTML('beforeend', modalHtml);

            const $modal = $('#hint-modal');
            $modal.modal('show');
            $modal.on('hidden.bs.modal', (event) => {
                document.getElementById('hint-modal').remove();
            });
        });
    }

    getBootstrap3ModalTemplate() {
        return '<div class="modal fade" id="hint-modal" tabindex="-1" role="dialog" aria-labelledby="hint-modal-label">\n' +
            '  <div class="modal-dialog modal-{{ size }}" role="document">\n' +
            '    <div class="modal-content">\n' +
            '      <div class="modal-header">\n' +
            '        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n' +
            '        <h4 class="modal-title" id="hint-modal-label">{{ title }}</h4>\n' +
            '      </div>\n' +
            '      <div class="modal-body">{{{ text }}}</div>\n' +
            '      <div class="modal-footer">\n' +
            '        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>\n' +
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
