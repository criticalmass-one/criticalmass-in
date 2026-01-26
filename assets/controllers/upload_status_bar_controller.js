import { Controller } from '@hotwired/stimulus';
import { uploadQueueManager, UploadStatus } from '../js/upload/UploadQueueManager';

export default class extends Controller {
    static targets = ['count', 'progress', 'progressBar'];

    connect() {
        this.setupBroadcastChannel();
        this.checkInitialStatus();
    }

    disconnect() {
        if (this.broadcastChannel) {
            this.broadcastChannel.close();
        }
    }

    async checkInitialStatus() {
        try {
            const status = await uploadQueueManager.getQueueStatus();
            this.updateDisplay(status);
        } catch (error) {
            console.warn('Could not check upload status:', error);
        }
    }

    setupBroadcastChannel() {
        if ('BroadcastChannel' in window) {
            this.broadcastChannel = new BroadcastChannel('upload-status');

            this.broadcastChannel.addEventListener('message', (event) => {
                const { type, data } = event.data;

                switch (type) {
                    case 'upload-added':
                    case 'upload-status-changed':
                    case 'upload-removed':
                    case 'completed-cleared':
                    case 'queue-cleared':
                        this.refreshStatus();
                        break;
                    case 'upload-progress':
                        this.updateProgress(data);
                        break;
                }
            });
        }
    }

    async refreshStatus() {
        try {
            const status = await uploadQueueManager.getQueueStatus();
            this.updateDisplay(status);
        } catch (error) {
            console.warn('Could not refresh upload status:', error);
        }
    }

    updateDisplay(status) {
        const activeCount = status.pending + status.uploading + status.retry;

        if (activeCount === 0) {
            this.hide();
            return;
        }

        this.show();

        if (this.hasCountTarget) {
            this.countTarget.textContent = this.getStatusText(status);
        }

        if (this.hasProgressBarTarget && status.entries.length > 0) {
            const uploadingEntries = status.entries.filter(e => e.status === UploadStatus.UPLOADING);
            if (uploadingEntries.length > 0) {
                const totalProgress = uploadingEntries.reduce((sum, e) => sum + (e.progress || 0), 0);
                const avgProgress = Math.round(totalProgress / uploadingEntries.length);
                this.progressBarTarget.style.width = `${avgProgress}%`;
                this.progressBarTarget.setAttribute('aria-valuenow', avgProgress);
            }
        }
    }

    updateProgress(data) {
        if (this.hasProgressBarTarget) {
            this.progressBarTarget.style.width = `${data.progress}%`;
            this.progressBarTarget.setAttribute('aria-valuenow', data.progress);
        }
    }

    getStatusText(status) {
        const parts = [];

        if (status.uploading > 0) {
            parts.push(`${status.uploading} wird hochgeladen`);
        }
        if (status.pending > 0) {
            parts.push(`${status.pending} wartend`);
        }
        if (status.retry > 0) {
            parts.push(`${status.retry} wird wiederholt`);
        }
        if (status.failed > 0) {
            parts.push(`${status.failed} fehlgeschlagen`);
        }

        return parts.join(' | ') || 'Keine aktiven Uploads';
    }

    show() {
        this.element.classList.remove('d-none');
    }

    hide() {
        this.element.classList.add('d-none');
    }

    async clearCompleted() {
        await uploadQueueManager.clearCompleted();
        this.refreshStatus();
    }
}
