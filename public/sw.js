const CACHE_VERSION = 'v1';
const UPLOAD_QUEUE_STORE = 'pending-uploads';

const broadcastChannel = new BroadcastChannel('upload-status');

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

self.addEventListener('sync', (event) => {
    if (event.tag.startsWith('upload-retry-')) {
        const queueId = event.tag.replace('upload-retry-', '');
        event.waitUntil(processRetryUpload(queueId));
    }
});

async function processRetryUpload(queueId) {
    try {
        const db = await openUploadDB();
        const entry = await getUploadEntry(db, parseInt(queueId, 10));

        if (!entry || entry.status === 'completed') {
            return;
        }

        broadcastChannel.postMessage({
            type: 'sw-upload-started',
            data: { queueId, fileName: entry.fileName }
        });

        const response = await fetch(entry.endpoint, {
            method: 'POST',
            body: entry.formData,
            credentials: 'include'
        });

        if (response.ok) {
            await updateUploadStatus(db, parseInt(queueId, 10), 'completed');
            broadcastChannel.postMessage({
                type: 'sw-upload-success',
                data: { queueId, fileName: entry.fileName }
            });
        } else {
            throw new Error(`Upload failed with status ${response.status}`);
        }
    } catch (error) {
        console.error('Background upload failed:', error);
        broadcastChannel.postMessage({
            type: 'sw-upload-error',
            data: { queueId, error: error.message }
        });

        throw error;
    }
}

function openUploadDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('criticalmass-uploads', 1);

        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve(request.result);
    });
}

function getUploadEntry(db, id) {
    return new Promise((resolve, reject) => {
        const transaction = db.transaction('uploads', 'readonly');
        const store = transaction.objectStore('uploads');
        const request = store.get(id);

        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve(request.result);
    });
}

function updateUploadStatus(db, id, status) {
    return new Promise((resolve, reject) => {
        const transaction = db.transaction('uploads', 'readwrite');
        const store = transaction.objectStore('uploads');
        const getRequest = store.get(id);

        getRequest.onerror = () => reject(getRequest.error);
        getRequest.onsuccess = () => {
            const entry = getRequest.result;
            if (entry) {
                entry.status = status;
                entry.updatedAt = Date.now();
                const putRequest = store.put(entry);
                putRequest.onerror = () => reject(putRequest.error);
                putRequest.onsuccess = () => resolve();
            } else {
                resolve();
            }
        };
    });
}

self.addEventListener('fetch', (event) => {
    if (event.request.method === 'POST' && event.request.url.includes('/addphoto')) {
        event.respondWith(handlePhotoUpload(event.request));
    }
});

async function handlePhotoUpload(request) {
    try {
        const response = await fetch(request.clone());
        return response;
    } catch (error) {
        if (!navigator.onLine) {
            const formData = await request.formData();
            const file = formData.get('file');

            if (file) {
                broadcastChannel.postMessage({
                    type: 'upload-queued-offline',
                    data: {
                        fileName: file.name,
                        fileSize: file.size
                    }
                });

                return new Response(JSON.stringify({
                    success: false,
                    offline: true,
                    message: 'Upload wird ausgeführt, sobald die Verbindung wiederhergestellt ist.'
                }), {
                    status: 202,
                    headers: { 'Content-Type': 'application/json' }
                });
            }
        }

        throw error;
    }
}
