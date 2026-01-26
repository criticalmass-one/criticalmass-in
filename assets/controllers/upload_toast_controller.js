import { Controller } from '@hotwired/stimulus';
import { Toast } from 'bootstrap';

export default class extends Controller {
    static targets = ['container'];

    connect() {
        this.setupBroadcastChannel();
        this.setupCustomEventListener();
    }

    disconnect() {
        if (this.broadcastChannel) {
            this.broadcastChannel.close();
        }
    }

    setupBroadcastChannel() {
        if ('BroadcastChannel' in window) {
            this.broadcastChannel = new BroadcastChannel('upload-status');

            this.broadcastChannel.addEventListener('message', (event) => {
                const { type, data } = event.data;

                switch (type) {
                    case 'show-toast':
                        this.showToast(data.type, data.message);
                        break;
                    case 'sw-upload-success':
                        this.showToast('success', `${data.fileName} im Hintergrund hochgeladen`);
                        break;
                    case 'sw-upload-error':
                        this.showToast('error', `Hintergrund-Upload fehlgeschlagen: ${data.error}`);
                        break;
                    case 'upload-queued-offline':
                        this.showToast('info', `${data.fileName} wird hochgeladen, sobald die Verbindung wiederhergestellt ist`);
                        break;
                }
            });
        }
    }

    setupCustomEventListener() {
        document.addEventListener('upload:toast', (event) => {
            this.showToast(event.detail.type, event.detail.message);
        });
    }

    showToast(type, message) {
        const toastId = `toast-${Date.now()}`;
        const bgClass = this.getBackgroundClass(type);
        const icon = this.getIcon(type);

        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="${icon} me-2"></i>
                        ${this.escapeHtml(message)}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Schließen"></button>
                </div>
            </div>
        `;

        this.containerTarget.insertAdjacentHTML('beforeend', toastHtml);

        const toastElement = document.getElementById(toastId);
        const toast = new Toast(toastElement, {
            autohide: true,
            delay: type === 'error' ? 8000 : 5000
        });

        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        }, { once: true });

        toast.show();
    }

    getBackgroundClass(type) {
        switch (type) {
            case 'success':
                return 'bg-success';
            case 'error':
                return 'bg-danger';
            case 'warning':
                return 'bg-warning text-dark';
            case 'info':
            default:
                return 'bg-info';
        }
    }

    getIcon(type) {
        switch (type) {
            case 'success':
                return 'fas fa-check-circle';
            case 'error':
                return 'fas fa-exclamation-circle';
            case 'warning':
                return 'fas fa-exclamation-triangle';
            case 'info':
            default:
                return 'fas fa-info-circle';
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}
