define(['TrackEntity', 'PositionMarker'], function() {
    TimelapseTrackEntity = function (trackId, polylineString, colorRed, colorGreen, colorBlue, username, backgroundImage) {
        this._trackId = trackId;

        this.setColor(colorRed, colorGreen, colorBlue);

        this._polyline = L.Polyline.fromEncoded(polylineString, { color: this._color });

        this._username = username;
        this._backgroundImage = backgroundImage;
    };

    // do not call constructor directly as this will execute the entity
    TimelapseTrackEntity.prototype = Object.create(TrackEntity.prototype);
    TimelapseTrackEntity.prototype.constructor = TimelapseTrackEntity;

    TimelapseTrackEntity.prototype._username = null;
    TimelapseTrackEntity.prototype._backgroundImage = null;
    TimelapseTrackEntity.prototype._currentLatLng = null;


    TimelapseTrackEntity.prototype.setCurrentLatLng = function(latLng) {
        this._currentLatLng = latLng;
    };

    TimelapseTrackEntity.prototype._createMarker = function() {
        if (!this._marker) {
            this._marker = new PositionMarker(this._currentLatLng, false, this._username, this._backgroundImage);

            this._marker.setColorRed(this.getColorRed());
            this._marker.setColorGreen(this.getColorGreen());
            this._marker.setColorBlue(this.getColorBlue());


            //this._initPopup();
        }
    };

    return TimelapseTrackEntity;
});