define(['Map', 'PositionMarker', 'TrackEntity', 'CityEntity', 'RideEntity', 'bootstrap-slider', 'dateformat'], function() {
    Timelapse = function(parentPage) {
        this._parentPage = parentPage;
    };

    Timelapse.prototype._initialized = false;
    Timelapse.prototype._map = null;
    Timelapse.prototype._trackLatLngs = [];
    Timelapse.prototype._marker = [];
    Timelapse.prototype._timer = null;

    Timelapse.prototype._initCallbackFunction = null;
    Timelapse.prototype._loadCallbackFunction = null;

    Timelapse.prototype._timeSlider = null;
    Timelapse.prototype._speedSlider = null;

    Timelapse.prototype._startDateTime = null;
    Timelapse.prototype._currentDateTime = null;
    Timelapse.prototype._endDateTime = null;

    Timelapse.prototype._loadedTracks = 0;

    Timelapse.prototype._options = {
        timeStep: 30000,
        baseTimeInterval: 100
    };

    Timelapse.prototype.setLoadCallbackFunction = function(callback) {
        this._loadCallbackFunction = callback;
    };

    Timelapse.prototype.setInitCallbackFunction = function(callback) {
        this._initCallbackFunction = callback;
    };

    Timelapse.prototype._loadAllTrackLatLngs = function() {
        for (var trackId in this._trackContainer.getList()) {
            this._loadTrackLatLngs(trackId);
        }
    };

    Timelapse.prototype._loadTrackLatLngs = function(trackId) {
        var trackUrl = 'http://criticalmass.cm/app_dev.php/berlin/2015-12-25/timelapse/load/' + trackId;

        var that = this;

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: trackUrl,
            cache: false,
            success: function(data) {
                that._trackLatLngs[trackId] = data;

                that._initPositionMarker(trackId);

                ++that._loadedTracks;

                that._loadCallbackFunction(that._loadedTracks);

                if (that._trackContainer.countEntities() == that._loadedTracks) {
                    that._initAfterLatLngLoad();
                }
            }
        });

    };

    Timelapse.prototype.init = function() {
        this._map = this._parentPage._map;
        this._trackContainer = this._parentPage._trackContainer;

        this._loadStyles();
        this._initSpeedSlider();
        this._initControls();
    };

    Timelapse.prototype.startInit = function() {
        this._loadAllTrackLatLngs();
    };

    Timelapse.prototype._initAfterLatLngLoad = function() {
        this._startDateTime = this._findEarliestDateTime();
        this._endDateTime = this._findLatestDateTime();

        this._initDateTime();
        this._initTimeSlider();

        this._initialized = true;

        this._initCallbackFunction();
    };

    Timelapse.prototype._loadStyles = function() {
        var $link = $('<link>', {
            rel: 'stylesheet',
            type: 'text/css',
            href: '/bundles/calderacriticalmasssite/css/external/bootstrap-slider.min.css'
        });

        $link.appendTo('head');
    };

    Timelapse.prototype._initTimeSlider = function() {
        var startTime = this._startDateTime.getTime();
        var endTime = this._endDateTime.getTime();

        var diff = startTime - endTime;

        var that = this;

        this._timeSlider = $('#time-slider-input').slider({
            id: 'time-slider',
            min: startTime,
            max: endTime,
            range: false,
            value: 0,
            step: 150000,
            tooltip: 'hide'
        }).on('change', function(values) {
            that._currentDateTime = new Date(values.value.newValue);

            that.stepForward();
        });
    };

    Timelapse.prototype._initSpeedSlider = function() {
        var that = this;

        this._speedSlider = $('#speed-slider-input').slider({
            id: 'speed-slider',
            min: 0.5,
            max: 10,
            range: false,
            value: 1,
            step: 0.5,
            tooltip: 'hide'
        }).on('change', function(values) {
            var speedFactor = (10.0 - values.value.newValue);

            that.stop();
            that.start(speedFactor);
        });
    };

    Timelapse.prototype._initDateTime = function() {
        this._currentDateTime = this._findEarliestDateTime();

        this._updateClocks();
    };

    Timelapse.prototype._initPositionMarker = function(trackId) {
        var firstLatLng = [this._trackLatLngs[trackId][0][1], this._trackLatLngs[trackId][0][2]];
        var track = this._trackContainer.getEntity(trackId);

        var marker = new PositionMarker(firstLatLng, false);
        marker.setColorRed(track.getColorRed());
        marker.setColorGreen(track.getColorGreen());
        marker.setColorBlue(track.getColorBlue());

        marker.addToMap(this._map);
        this._marker[trackId] = marker;
    };

    Timelapse.prototype._initControls = function() {
        var that = this;

        $('#control-buttons').find('#step-backward-button').on('click', function() {
            that.stepBackward();
        });

        $('#control-buttons').find('#pause-button').on('click', function() {
            that.pause();
        });

        $('#control-buttons').find('#stop-button').on('click', function() {
            that.stop();
        });

        $('#control-buttons').find('#play-button').on('click', function() {
            that.start();
        });

        $('#control-buttons').find('#step-forward-button').on('click', function() {
            that.stepForward();
        });
    };

    Timelapse.prototype.start = function(speedFactor) {
        if (!this._initialized) {
            this.startInit();
        } else {
            this.stepForward();

            var that = this;

            var interval = this._options.baseTimeInterval;

            if (speedFactor) {
                interval *= speedFactor;
            }

            this._timer = window.setInterval(function () {
                that.stepForward();

            }, interval);
        }
    };

    Timelapse.prototype.stop = function() {
        this.pause();
    };

    Timelapse.prototype.pause = function() {
        clearInterval(this._timer);
        this._timer = null;
    };

    Timelapse.prototype._updateClocks = function() {
        $('#timelapse-time-clock').html(this._currentDateTime.format('HH:MM'));

        var microsecondsDiff = this._currentDateTime.getTime() - this._startDateTime.getTime();
        var minutesDiff = microsecondsDiff / 1000 / 60;

        $('#timelapse-time-elapsed').html(minutesDiff);
    };

    Timelapse.prototype.stepBackward = function() {
        this._currentDateTime = new Date(this._currentDateTime.getTime() - this._options.timeStep);

        this._timeSlider.slider('setValue', this._currentDateTime.getTime(), false, false);

        this._updateClocks();

        for (var trackId in this._trackLatLngs) {
            var prevLatLng = this._findPreviousLatLngForDateTime(trackId, this._currentDateTime);

            if (prevLatLng) {
                var marker = this._marker[trackId];

                if (!marker.isMapped()) {
                    marker.addToMap(this._map);
                }

                marker.setLatLng(prevLatLng);
            } else {
                this._marker[trackId].removeFromMap(this._map);
            }
        }
    };

    Timelapse.prototype.stepForward = function() {
        this._currentDateTime = new Date(this._currentDateTime.getTime() + this._options.timeStep);

        this._timeSlider.slider('setValue', this._currentDateTime.getTime(), false, false);

        this._updateClocks();

        for (var trackId in this._trackLatLngs) {
            var nextLatLng = this._findNextLatLngForDateTime(trackId, this._currentDateTime);

            if (nextLatLng) {
                var marker = this._marker[trackId];

                if (!marker.isMapped()) {
                    marker.addToMap(this._map);
                }

                marker.setLatLng(nextLatLng);
            } else {
                this._marker[trackId].removeFromMap(this._map);
            }
        }
    };

    Timelapse.prototype._findEarliestDateTime = function() {
        var earliestDatetime = null;

        for (var trackId in this._trackLatLngs) {
            var dateTime = new Date(this._trackLatLngs[trackId][0][0]);

            if (!earliestDatetime || earliestDatetime.getTime() > dateTime.getTime()) {
                earliestDatetime = dateTime;
            }
        }

        return earliestDatetime;
    };

    Timelapse.prototype._findLatestDateTime = function() {
        var latestDateTime = null;

        for (var trackId in this._trackLatLngs) {
            var counter = this._trackLatLngs[trackId].length;
            var dateTime = new Date(this._trackLatLngs[trackId][counter - 1][0]);

            if (!latestDateTime || latestDateTime.getTime() < dateTime.getTime()) {
                latestDateTime = dateTime;
            }
        }

        return latestDateTime;
    };


    Timelapse.prototype._findNextLatLngForDateTime = function(trackId, dateTime) {
        for (var index in this._trackLatLngs[trackId]) {
            var dateTimeLatLng = this._trackLatLngs[trackId][index];
            var trackDateTime = new Date(dateTimeLatLng[0]);

            if (trackDateTime.getTime() > dateTime.getTime()) {

                return [dateTimeLatLng[1], dateTimeLatLng[2]];
            }
        }

        return null;
    };

    Timelapse.prototype._findPreviousLatLngForDateTime = function(trackId, dateTime) {
        for (var index in this._trackLatLngs[trackId]) {
            var dateTimeLatLng = this._trackLatLngs[trackId][index];
            var trackDateTime = new Date(dateTimeLatLng[0]);

            if (trackDateTime.getTime() < dateTime.getTime()) {
                trackDateTime = dateTime;
            } else {
                return [dateTimeLatLng[1], dateTimeLatLng[2]];
            }
        }

        return null;
    };

    return Timelapse;
});