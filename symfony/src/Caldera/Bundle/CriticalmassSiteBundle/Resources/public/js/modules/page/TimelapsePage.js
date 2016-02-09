define(['Map', 'PositionMarker', 'TrackEntity', 'bootstrap-slider'], function() {
    TimelapsePage = function(context, options) {


    };

    TimelapsePage.prototype._map = null;
    TimelapsePage.prototype._tracks = [];
    TimelapsePage.prototype._trackLatLngs = [];
    TimelapsePage.prototype._marker = [];
    TimelapsePage.prototype._slider = null;
    TimelapsePage.prototype._currentDateTime = null;
    TimelapsePage.prototype._timer = null;
    TimelapsePage.prototype._options = {
        timeStep: 30000,
        baseTimeInterval: 100
    };


    TimelapsePage.prototype.init = function() {
        this._loadStyles();
        this._initSlider();
        this._initMap();
        this._initDateTime();
        this._initControls();
    };

    TimelapsePage.prototype._loadStyles = function() {
        var $link = $('<link>', {
            rel: 'stylesheet',
            type: 'text/css',
            href: '/bundles/calderacriticalmasssite/css/external/bootstrap-slider.min.css'
        });

        $link.appendTo('head');
    };

    TimelapsePage.prototype._initSlider = function() {
        var that = this;

        $('#speed-slider-input').slider({
            id: 'speed-slider',
            min: 0.5,
            max: 10,
            range: false,
            value: 1,
            step: 0.5,
            tooltip: 'show'
        }).on('change', function(values) {
            var speedFactor = (10.0 - values.value.newValue);

            that.stop();
            that.start(speedFactor);
        });
    };

    TimelapsePage.prototype._initDateTime = function() {
        this._currentDateTime = new Date('2016-01-29T17:34:11Z');
    };

    TimelapsePage.prototype._initMap = function() {
        this._map = new Map('map');

        this._map.setView([53.545697,9.952488], 14);
    };

    TimelapsePage.prototype.addTrack = function(trackId, colorRed, colorGreen, colorBlue) {
        var track = new TrackEntity();
        track.setColors(colorRed, colorGreen, colorBlue);

        this._tracks[trackId] = track;

        this._loadTrackLatLngs(trackId);
    };

    TimelapsePage.prototype._loadTrackLatLngs = function(trackId) {
        var trackUrl = 'http://www.criticalmass.cm/app_dev.php/hamburg/2016-01-29/timelapse/load/' + trackId;

        var that = this;

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: trackUrl,
            cache: false,
            success: function(data) {
                that._trackLatLngs[trackId] = data;

                that._initPositionMarker(trackId);
            },
            error: function(data, status) {
                alert('Fooooo' + status);
            }
        });

    };

    TimelapsePage.prototype._initPositionMarker = function(trackId) {
        var firstLatLng = [this._trackLatLngs[trackId][0][1], this._trackLatLngs[trackId][0][2]];
        var track = this._tracks[trackId];

        var marker = new PositionMarker(firstLatLng, false);
        marker.setColorRed(track.getColorRed());
        marker.setColorGreen(track.getColorGreen());
        marker.setColorBlue(track.getColorBlue());

        marker.addToMap(this._map);

        this._marker[trackId] = marker;
    };

    TimelapsePage.prototype._initControls = function() {
        var that = this;

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

    TimelapsePage.prototype.start = function(speedFactor) {
        alert(this._findLatestDateTime());
        this.stepForward();

        var that = this;

        var interval = this._options.baseTimeInterval;

        if (speedFactor) {
            interval *= speedFactor;
        }

        this._timer = window.setInterval(function () {
            that.stepForward();

        }, interval);
    };

    TimelapsePage.prototype.stop = function() {
        clearInterval(this._timer);
        this._timer = null;
    };

    TimelapsePage.prototype._findNextLatLngForDateTime = function(trackId, dateTime) {
        for (var index in this._trackLatLngs[trackId]) {
            var dateTimeLatLng = this._trackLatLngs[trackId][index];
            var trackDateTime = new Date(dateTimeLatLng[0]);

            if (trackDateTime.getTime() > dateTime.getTime()) {
                return [dateTimeLatLng[1], dateTimeLatLng[2]];
            }
        }

        return null;
    };

    TimelapsePage.prototype.stepForward = function() {
        this._currentDateTime = new Date(this._currentDateTime.getTime() + this._options.timeStep);

        for (var trackId in this._trackLatLngs) {
            var nextLatLng = this._findNextLatLngForDateTime(trackId, this._currentDateTime);

            if (nextLatLng) {
                this._marker[trackId].setLatLng(nextLatLng);
            } else {
                this._marker[trackId].removeFromMap(this._map);
            }
        }
    };

    TimelapsePage.prototype._findEarliestDateTime = function() {
        var earliestDatetime = null;

        for (var trackId in this._trackLatLngs) {
            var dateTime = new Date(this._trackLatLngs[trackId][0][0]);

            if (!earliestDatetime || earliestDatetime.getTime() > dateTime.getTime()) {
                earliestDatetime = dateTime;
            }
        }

        return earliestDatetime;
    };

    TimelapsePage.prototype._findLatestDateTime = function() {
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

    return TimelapsePage;
});