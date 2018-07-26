define(['dropzone'], function (Dropzone) {
    UploadPhotoPage = function (context, options) {
        this._loadStyles();
    };

    UploadPhotoPage.prototype._loadStyles = function () {
        var $link = $('<link>', {
            rel: 'stylesheet',
            type: 'text/css',
            href: '/css/external/dropzone.basic.css'
        });

        $link.appendTo('head');
    };

    return UploadPhotoPage;
});
