import { Controller } from '@hotwired/stimulus';
import Uppy from '@uppy/core';
import Dashboard from '@uppy/dashboard';
import XHRUpload from '@uppy/xhr-upload';
import Compressor from '@uppy/compressor';
import { uploadQueueManager, UploadStatus } from '../js/upload/UploadQueueManager';

import German from '@uppy/locales/lib/de_DE';

export default class extends Controller {
    static targets = ['dashboard'];

    static values = {
        endpoint: String,
        maxFiles: { type: Number, default: 50 },
        maxFileSize: { type: Number, default: 20 * 1024 * 1024 },
        allowedFileTypes: { type: Array, default: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'] }
    };

    connect() {
        this.fileQueueMap = new Map();
        this.initUppy();
        this.registerServiceWorker();
        this.setupBroadcastChannel();
    }

    disconnect() {
        if (this.uppy) {
            this.uppy.destroy();
        }
        if (this.broadcastChannel) {
            this.broadcastChannel.close();
        }
    }

    initUppy() {
        this.uppy = new Uppy({
            id: 'photo-upload',
            locale: German,
            restrictions: {
                maxNumberOfFiles: this.maxFilesValue,
                maxFileSize: this.maxFileSizeValue,
                allowedFileTypes: this.allowedFileTypesValue
            },
            autoProceed: false,
            meta: {
                endpoint: this.endpointValue
            }
        });

        this.uppy.use(Dashboard, {
            target: this.dashboardTarget,
            inline: true,
            width: '100%',
            height: 450,
            showProgressDetails: true,
            showRemoveButtonAfterComplete: true,
            proudlyDisplayPoweredByUppy: false,
            note: 'Nur Bilder (JPEG, PNG, GIF, WebP), max. 20 MB pro Datei',
            locale: {
                strings: {
                    dropPasteFiles: 'Ziehe Dateien hierher oder %{browseFiles}',
                    browseFiles: 'durchsuche Dateien',
                    uploadComplete: 'Upload abgeschlossen',
                    uploadFailed: 'Upload fehlgeschlagen',
                    uploading: 'Wird hochgeladen',
                    complete: 'Fertig',
                    uploadXFiles: {
                        '0': '%{smart_count} Datei hochladen',
                        '1': '%{smart_count} Dateien hochladen'
                    },
                    uploadXNewFiles: {
                        '0': '+%{smart_count} Datei hochladen',
                        '1': '+%{smart_count} Dateien hochladen'
                    },
                    xFilesSelected: {
                        '0': '%{smart_count} Datei ausgewählt',
                        '1': '%{smart_count} Dateien ausgewählt'
                    }
                }
            }
        });

        this.uppy.use(Compressor, {
            quality: 0.85,
            limit: 2
        });

        this.uppy.use(XHRUpload, {
            endpoint: this.endpointValue,
            fieldName: 'file',
            withCredentials: true,
            formData: true,
            limit: 3,
            timeout: 120000
        });

        this.setupEventHandlers();
    }

    setupEventHandlers() {
        this.uppy.on('file-added', async (file) => {
            const queueId = await uploadQueueManager.addToQueue({
                name: file.name,
                size: file.size,
                type: file.type,
                endpoint: this.endpointValue,
                uppyFileId: file.id
            });

            this.fileQueueMap.set(file.id, queueId);
        });

        this.uppy.on('file-removed', async (file) => {
            const queueId = this.fileQueueMap.get(file.id);
            if (queueId) {
                await uploadQueueManager.removeEntry(queueId);
                this.fileQueueMap.delete(file.id);
            }
        });

        this.uppy.on('upload-start', async (files) => {
            for (const file of files) {
                const queueId = this.fileQueueMap.get(file.id);
                if (queueId) {
                    await uploadQueueManager.updateStatus(queueId, UploadStatus.UPLOADING);
                }
            }
        });

        this.uppy.on('upload-progress', async (file, progress) => {
            const queueId = this.fileQueueMap.get(file.id);
            if (queueId && progress.bytesTotal > 0) {
                const percent = Math.round((progress.bytesUploaded / progress.bytesTotal) * 100);
                await uploadQueueManager.updateProgress(queueId, percent);
            }
        });

        this.uppy.on('upload-success', async (file, response) => {
            const queueId = this.fileQueueMap.get(file.id);
            if (queueId) {
                await uploadQueueManager.markComplete(queueId);
            }
            this.showToast('success', `${file.name} erfolgreich hochgeladen`);
        });

        this.uppy.on('upload-error', async (file, error, response) => {
            const queueId = this.fileQueueMap.get(file.id);
            if (queueId) {
                if (this.isNetworkError(error)) {
                    await uploadQueueManager.markForRetry(queueId);
                    this.queueForBackgroundSync(file, queueId);
                } else {
                    await uploadQueueManager.markFailed(queueId, error);
                }
            }
            this.showToast('error', `Fehler beim Hochladen von ${file.name}`);
        });

        this.uppy.on('complete', (result) => {
            if (result.successful.length > 0) {
                this.showToast('success', `${result.successful.length} Datei(en) erfolgreich hochgeladen`);
            }
            if (result.failed.length > 0) {
                this.showToast('warning', `${result.failed.length} Datei(en) fehlgeschlagen`);
            }
        });

        this.uppy.on('restriction-failed', (file, error) => {
            this.showToast('error', error.message);
        });
    }

    isNetworkError(error) {
        return (
            !navigator.onLine ||
            error.message?.includes('network') ||
            error.message?.includes('Network') ||
            error.message?.includes('fetch') ||
            error.message?.includes('Failed to fetch')
        );
    }

    async queueForBackgroundSync(file, queueId) {
        if ('serviceWorker' in navigator && 'SyncManager' in window) {
            try {
                const registration = await navigator.serviceWorker.ready;
                await registration.sync.register(`upload-retry-${queueId}`);
            } catch (e) {
                console.warn('Background sync registration failed:', e);
            }
        }
    }

    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js', {
                    scope: '/'
                });

                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'activated') {
                            console.log('Service Worker updated');
                        }
                    });
                });
            } catch (error) {
                console.warn('Service Worker registration failed:', error);
            }
        }
    }

    setupBroadcastChannel() {
        if ('BroadcastChannel' in window) {
            this.broadcastChannel = new BroadcastChannel('upload-status');

            this.broadcastChannel.addEventListener('message', (event) => {
                const { type, data } = event.data;

                if (type === 'sw-upload-success') {
                    this.showToast('success', `${data.fileName} im Hintergrund hochgeladen`);
                } else if (type === 'sw-upload-error') {
                    this.showToast('error', `Hintergrund-Upload von ${data.fileName} fehlgeschlagen`);
                }
            });
        }
    }

    showToast(type, message) {
        if (this.broadcastChannel) {
            this.broadcastChannel.postMessage({
                type: 'show-toast',
                data: { type, message, timestamp: Date.now() }
            });
        }

        const event = new CustomEvent('upload:toast', {
            bubbles: true,
            detail: { type, message }
        });
        this.element.dispatchEvent(event);
    }
}
