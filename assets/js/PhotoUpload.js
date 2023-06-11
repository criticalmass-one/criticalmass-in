import Dropzone from 'dropzone/dist/dropzone';

export default class PhotoUpload {
    constructor(photoDropzoneElement, options) {
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const photoDropzoneElement = document.getElementById('photo-dropzone');

    if (photoDropzoneElement) {
        new PhotoUpload(photoDropzoneElement);
    }
});