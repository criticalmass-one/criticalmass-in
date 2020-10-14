define(['jquery'], function () {

    WritePost = function (context, options) {
        this._$form = $(context);
        this._$checkbox = this._$form.find('#form-post-share-location');
        this._$button = this._$form.find('#button-submit-post');

        this._options = options;

        this._initEvents();
    };

    WritePost.prototype._disableButton = function () {
        this._$button.prop('disabled', 'disabled');
    };

    WritePost.prototype._getLocation = function () {
        var that = this;

        if (navigator.geolocation) {
            var options = {
                enableHighAccuracy: false,
                timeout: 2500,
                maximumAge: 0
            };

            navigator.geolocation.getCurrentPosition(function (position) {
                that._updatePositionInputFields(position);
            }, function () {
                that._submitForm();
            }, options);
        } else {
            this._submitForm();
        }
    };

    WritePost.prototype._isLocationChecked = function () {
        return this._$checkbox.attr('checked') == 'checked';
    };

    WritePost.prototype._submitForm = function () {
        this._$form.submit();
    };

    WritePost.prototype._updatePositionInputFields = function (position) {
        var coord = position.coords;

        //alert(JSON.stringify(position));
        $('#form_latitude').val(coord.latitude);
        $('#form_longitude').val(coord.longitude);

        this._submitForm();
    };

    WritePost.prototype._preventDefault = function () {
        this._$form.off('submit');
    };

    WritePost.prototype._initEvents = function () {
        var that = this;

        this._$form.on('submit', function (element) {
            element.preventDefault();

            that._preventDefault();

            that._disableButton();

            if (that._isLocationChecked()) {
                that._getLocation();
            } else {
                that._submitForm();
            }
        });
    };

    return WritePost;
});
