import { Controller } from '@hotwired/stimulus';
import Dropzone from 'dropzone';

// We initialise Dropzone manually on the target element, so disable the global
// auto-discovery (which would otherwise also try to attach to `.dropzone` elements).
Dropzone.autoDiscover = false;

/**
 * Wraps Dropzone v5 for the bulk GPX/FIT upload. Each file is uploaded individually to
 * the per-file backend endpoint (#1383); the JSON status (matched/parked/duplicate/error)
 * is surfaced per file. After all uploads finish, a link to the review list is revealed.
 */
export default class extends Controller {
    static targets = ['dropzone', 'reviewLink'];

    static values = {
        uploadUrl: String,
        reviewUrl: String,
        csrfToken: String,
        maxFiles: { type: Number, default: 500 },
        parallelUploads: { type: Number, default: 4 },
    };

    connect() {
        this.dropzone = new Dropzone(this.dropzoneTarget, {
            url: this.uploadUrlValue,
            method: 'post',
            paramName: 'file',
            maxFiles: this.maxFilesValue,
            parallelUploads: this.parallelUploadsValue,
            uploadMultiple: false,
            autoProcessQueue: true,
            acceptedFiles: '.gpx,.fit',
            addRemoveLinks: false,
            timeout: 0,
            params: {
                _token: this.csrfTokenValue,
            },
            dictDefaultMessage: 'GPX-/FIT-Dateien hierher ziehen oder klicken zum Auswählen',
            dictInvalidFileType: 'Nur .gpx- und .fit-Dateien werden akzeptiert.',
            dictResponseError: 'Beim Hochladen ist ein Fehler aufgetreten.',
        });

        this.dropzone.on('success', (file, response) => this.handleSuccess(file, response));
        this.dropzone.on('error', (file, message) => this.handleError(file, message));
        this.dropzone.on('queuecomplete', () => this.handleQueueComplete());
    }

    disconnect() {
        if (this.dropzone) {
            this.dropzone.destroy();
            this.dropzone = null;
        }
    }

    handleSuccess(file, response) {
        const status = response && response.status ? response.status : 'matched';
        const message = response && response.message ? response.message : '';

        const labels = {
            matched: { css: 'dz-cm-matched', text: message || 'Einer Tour zugeordnet.' },
            parked: { css: 'dz-cm-parked', text: message || 'Keiner Tour zugeordnet — zum Prüfen geparkt.' },
            duplicate: { css: 'dz-cm-duplicate', text: message || 'Diese Datei wurde bereits hochgeladen.' },
        };

        const label = labels[status] || labels.matched;

        file.previewElement.classList.add(label.css);
        this.setFileStatus(file, label.text);
    }

    handleError(file, message) {
        const text = typeof message === 'string'
            ? message
            : (message && message.message ? message.message : 'Fehler beim Hochladen.');

        file.previewElement.classList.add('dz-cm-error');
        this.setFileStatus(file, text);
    }

    handleQueueComplete() {
        if (this.hasReviewLinkTarget) {
            this.reviewLinkTarget.classList.remove('d-none');
        }
    }

    setFileStatus(file, text) {
        if (!file.previewElement) {
            return;
        }

        const node = file.previewElement.querySelector('[data-dz-errormessage]');

        if (node) {
            node.textContent = text;
        }
    }
}
