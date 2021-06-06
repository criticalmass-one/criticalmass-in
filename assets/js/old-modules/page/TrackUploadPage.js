define([], function () {
    TrackUploadPage = function (context, options) {
        var $uploadForm = $('form#track-upload-form');
        var $uploadButton = $('button#track-upload-button');

        $uploadButton.on('click', function () {
            $uploadButton.prop('disabled', 'true');
            $uploadButton.html('Einen Moment bitte…');

            $uploadForm.submit();
        });
    };

    return TrackUploadPage;
});