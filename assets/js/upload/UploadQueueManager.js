import { openDB } from 'idb';

const DB_NAME = 'criticalmass-uploads';
const DB_VERSION = 1;
const STORE_NAME = 'uploads';

export const UploadStatus = {
    PENDING: 'pending',
    UPLOADING: 'uploading',
    COMPLETED: 'completed',
    FAILED: 'failed',
    RETRY: 'retry'
};

class UploadQueueManager {
    constructor() {
        this.db = null;
        this.broadcastChannel = null;
        this.initPromise = this.init();
    }

    async init() {
        this.db = await openDB(DB_NAME, DB_VERSION, {
            upgrade(db) {
                if (!db.objectStoreNames.contains(STORE_NAME)) {
                    const store = db.createObjectStore(STORE_NAME, {
                        keyPath: 'id',
                        autoIncrement: true
                    });
                    store.createIndex('status', 'status');
                    store.createIndex('createdAt', 'createdAt');
                }
            }
        });

        if ('BroadcastChannel' in window) {
            this.broadcastChannel = new BroadcastChannel('upload-status');
        }
    }

    async ensureReady() {
        await this.initPromise;
    }

    async addToQueue(fileData) {
        await this.ensureReady();

        const entry = {
            fileName: fileData.name,
            fileSize: fileData.size,
            fileType: fileData.type,
            endpoint: fileData.endpoint,
            status: UploadStatus.PENDING,
            progress: 0,
            createdAt: Date.now(),
            updatedAt: Date.now(),
            retryCount: 0,
            uppyFileId: fileData.uppyFileId || null
        };

        const id = await this.db.add(STORE_NAME, entry);
        this.broadcast('upload-added', { id, ...entry });

        return id;
    }

    async updateStatus(id, status, additionalData = {}) {
        await this.ensureReady();

        const tx = this.db.transaction(STORE_NAME, 'readwrite');
        const entry = await tx.store.get(id);

        if (entry) {
            Object.assign(entry, {
                status,
                updatedAt: Date.now(),
                ...additionalData
            });

            await tx.store.put(entry);
            this.broadcast('upload-status-changed', entry);
        }

        await tx.done;
    }

    async updateProgress(id, progress) {
        await this.ensureReady();

        const tx = this.db.transaction(STORE_NAME, 'readwrite');
        const entry = await tx.store.get(id);

        if (entry) {
            entry.progress = progress;
            entry.updatedAt = Date.now();
            await tx.store.put(entry);
            this.broadcast('upload-progress', { id, progress, fileName: entry.fileName });
        }

        await tx.done;
    }

    async markComplete(id) {
        await this.updateStatus(id, UploadStatus.COMPLETED, { progress: 100 });
    }

    async markFailed(id, error = null) {
        await this.updateStatus(id, UploadStatus.FAILED, { error: error?.message || error });
    }

    async markForRetry(id) {
        await this.ensureReady();

        const tx = this.db.transaction(STORE_NAME, 'readwrite');
        const entry = await tx.store.get(id);

        if (entry) {
            entry.status = UploadStatus.RETRY;
            entry.retryCount = (entry.retryCount || 0) + 1;
            entry.updatedAt = Date.now();
            await tx.store.put(entry);
            this.broadcast('upload-retry', entry);
        }

        await tx.done;
    }

    async getEntry(id) {
        await this.ensureReady();
        return this.db.get(STORE_NAME, id);
    }

    async getQueueStatus() {
        await this.ensureReady();

        const all = await this.db.getAll(STORE_NAME);

        return {
            total: all.length,
            pending: all.filter(e => e.status === UploadStatus.PENDING).length,
            uploading: all.filter(e => e.status === UploadStatus.UPLOADING).length,
            completed: all.filter(e => e.status === UploadStatus.COMPLETED).length,
            failed: all.filter(e => e.status === UploadStatus.FAILED).length,
            retry: all.filter(e => e.status === UploadStatus.RETRY).length,
            entries: all
        };
    }

    async getPendingUploads() {
        await this.ensureReady();

        const all = await this.db.getAll(STORE_NAME);
        return all.filter(e =>
            e.status === UploadStatus.PENDING ||
            e.status === UploadStatus.RETRY ||
            e.status === UploadStatus.UPLOADING
        );
    }

    async getFailedUploads() {
        await this.ensureReady();

        const all = await this.db.getAll(STORE_NAME);
        return all.filter(e => e.status === UploadStatus.FAILED);
    }

    async removeEntry(id) {
        await this.ensureReady();
        await this.db.delete(STORE_NAME, id);
        this.broadcast('upload-removed', { id });
    }

    async clearCompleted() {
        await this.ensureReady();

        const tx = this.db.transaction(STORE_NAME, 'readwrite');
        const all = await tx.store.getAll();

        for (const entry of all) {
            if (entry.status === UploadStatus.COMPLETED) {
                await tx.store.delete(entry.id);
            }
        }

        await tx.done;
        this.broadcast('completed-cleared', {});
    }

    async clearAll() {
        await this.ensureReady();
        await this.db.clear(STORE_NAME);
        this.broadcast('queue-cleared', {});
    }

    broadcast(type, data) {
        if (this.broadcastChannel) {
            this.broadcastChannel.postMessage({ type, data, timestamp: Date.now() });
        }
    }

    onMessage(callback) {
        if (this.broadcastChannel) {
            this.broadcastChannel.addEventListener('message', (event) => {
                callback(event.data);
            });
        }
    }
}

export const uploadQueueManager = new UploadQueueManager();
export default UploadQueueManager;
