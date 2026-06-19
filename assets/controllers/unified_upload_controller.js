import { Controller } from '@hotwired/stimulus';
import Uppy from '@uppy/core';
import Dashboard from '@uppy/dashboard';
import XHRUpload from '@uppy/xhr-upload';
import German from '@uppy/locales/lib/de_DE.js';

import '@uppy/core/dist/style.min.css';
import '@uppy/dashboard/dist/style.min.css';

const ACCEPTED_FILE_TYPES = ['.gpx', '.fit', '.jpg', '.jpeg', '.png', '.webp', '.gif', '.heic', '.heif'];

/**
 * Unified upload via Uppy. GPX/FIT tracks and image files are dropped together and
 * each is uploaded individually (one request per file) to the dispatcher endpoint.
 * The per-file JSON status (matched/parked/staged/duplicate/error) is surfaced below
 * the dashboard; once everything is done, a link to the review page is revealed.
 *
 * No Compressor plugin is used on purpose — image bytes (and their EXIF date/GPS,
 * which the photo matching relies on) are uploaded untouched.
 */
export default class extends Controller {
    static targets = ['dashboard', 'results', 'reviewLink'];

    static values = {
        uploadUrl: String,
        csrfToken: String,
    };

    connect() {
        this.uppy = new Uppy({
            autoProceed: true,
            locale: German,
            restrictions: {
                allowedFileTypes: ACCEPTED_FILE_TYPES,
            },
            meta: {
                _token: this.csrfTokenValue,
            },
        });

        this.uppy.use(Dashboard, {
            target: this.dashboardTarget,
            inline: true,
            width: '100%',
            height: 360,
            proudlyDisplayPoweredByUppy: false,
            showProgressDetails: true,
            note: 'GPX-/FIT-Tracks und Bilder (JPG, PNG, WebP, GIF, HEIC) — einfach alles zusammen hierher ziehen.',
        });

        this.uppy.use(XHRUpload, {
            endpoint: this.uploadUrlValue,
            method: 'post',
            fieldName: 'file',
            formData: true,
            bundle: false,
            limit: 4,
            timeout: 0,
            allowedMetaFields: ['_token'],
            getResponseError: (responseText) => this.extractError(responseText),
        });

        this.uppy.on('upload-success', (file, response) => this.handleSuccess(file, response));
        this.uppy.on('upload-error', (file, error) => this.handleError(file, error));
        this.uppy.on('complete', () => this.revealReviewLink());
    }

    disconnect() {
        if (this.uppy) {
            this.uppy.destroy();
            this.uppy = null;
        }
    }

    handleSuccess(file, response) {
        const body = (response && response.body) || {};
        const status = body.status || 'matched';

        this.addResult(file.name, status, body.message || this.defaultMessage(status));
    }

    handleError(file, error) {
        const message = (error && error.message) ? error.message : 'Fehler beim Hochladen.';

        this.addResult(file.name, 'error', message);
    }

    extractError(responseText) {
        try {
            const data = JSON.parse(responseText);

            if (data && data.message) {
                return new Error(data.message);
            }
        } catch (e) {
            // fall through to the generic message
        }

        return new Error('Beim Hochladen ist ein Fehler aufgetreten.');
    }

    revealReviewLink() {
        if (this.hasReviewLinkTarget) {
            this.reviewLinkTarget.classList.remove('d-none');
        }
    }

    addResult(name, status, message) {
        if (!this.hasResultsTarget) {
            return;
        }

        this.resultsTarget.classList.remove('d-none');

        const badge = this.badgeFor(status);

        const item = document.createElement('li');
        item.className = 'list-group-item d-flex align-items-center';
        item.innerHTML = `
            <span class="badge ${badge.css} me-2">${badge.label}</span>
            <span class="text-truncate me-2 fw-semibold">${this.escape(name)}</span>
            <span class="text-muted small ms-auto text-end">${this.escape(message)}</span>
        `;

        this.resultsTarget.appendChild(item);
    }

    badgeFor(status) {
        const badges = {
            matched: { css: 'text-bg-success', label: 'Zugeordnet' },
            staged: { css: 'text-bg-info', label: 'Gespeichert' },
            parked: { css: 'text-bg-warning', label: 'Geparkt' },
            duplicate: { css: 'text-bg-secondary', label: 'Duplikat' },
            error: { css: 'text-bg-danger', label: 'Fehler' },
        };

        return badges[status] || badges.matched;
    }

    defaultMessage(status) {
        const messages = {
            matched: 'Einer Tour zugeordnet.',
            staged: 'Bild gespeichert — du kannst es gleich zuordnen.',
            parked: 'Keiner Tour zugeordnet — zum Prüfen geparkt.',
            duplicate: 'Wurde bereits hochgeladen.',
        };

        return messages[status] || '';
    }

    escape(value) {
        const div = document.createElement('div');
        div.textContent = String(value);

        return div.innerHTML;
    }
}
