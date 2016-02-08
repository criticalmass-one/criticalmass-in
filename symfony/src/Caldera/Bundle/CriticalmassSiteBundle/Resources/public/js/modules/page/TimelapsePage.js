define(['Map', 'PositionMarker', 'bootstrap-slider'], function() {
    TimelapsePage = function(context, options) {
        this.loadTrackLatLngs(73);

    };

    TimelapsePage.prototype._map = null;
    TimelapsePage.prototype._tracks = [];
    TimelapsePage.prototype._marker = [];
    TimelapsePage.prototype._currentDateTime = null;

    TimelapsePage.prototype.init = function() {
        this._loadStyles();
        this._initSlider();
        this._initMap();
        this._initDateTime();
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
        $("#ex6").slider({
            id: "fooSlider",
            min: 0,
            max: 5,
            range: false,
            value: 2,
            tooltip: 'show'
        });
    };

    TimelapsePage.prototype._initDateTime = function() {
        this._currentDateTime = new Date('2016-01-29T17:34:11Z');
    };

    TimelapsePage.prototype._initMap = function() {
        this._map = new Map('map');

        this._map.setView([53.545697,9.952488], 14);
    };

    TimelapsePage.prototype.loadTrackLatLngs = function(trackId) {
        var trackUrl = 'http://www.criticalmass.cm/app_dev.php/hamburg/2016-01-29/timelapse/load/73';

        var that = this;

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: trackUrl,
            cache: false,
            success: function(data) {
                that._tracks[trackId] = data;

                that._initPositionMarker(trackId);
                that.start();
            },
            error: function(data, status) {
                alert('Fooooo' + status);
            }
        });

    };

    TimelapsePage.prototype._initPositionMarker = function(trackId) {
        var firstLatLng = [this._tracks[trackId][0][1], this._tracks[trackId][0][2]];

        this._marker[trackId] = new PositionMarker(firstLatLng, false);
        this._marker[trackId].addToMap(this._map);
    };

    TimelapsePage.prototype.start = function() {
        this.step();

        var that = this;
        this._timer = window.setInterval(function () {
            that.step();

        }, 5);
    };

    TimelapsePage.prototype._findNextLatLngForDateTime = function(trackId, dateTime) {
        for (var index in this._tracks[trackId]) {
            var dateTimeLatLng = this._tracks[trackId][index];
            var trackDateTime = new Date(dateTimeLatLng[0]);

            if (trackDateTime.getTime() > dateTime.getTime()) {
                return [dateTimeLatLng[1], dateTimeLatLng[2]];
            }
        }

        return null;
    };

    TimelapsePage.prototype.step = function() {
        var stepDiff = 30000;

        this._currentDateTime = new Date(this._currentDateTime.getTime() + stepDiff);

        for (var trackId in this._tracks) {
            var nextLatLng = this._findNextLatLngForDateTime(trackId, this._currentDateTime);

            if (nextLatLng) {
                this._marker[trackId].setLatLng(nextLatLng);
            } else {
                this._marker[trackId].removeFromMap(this._map);
            }
        }
    };

    return TimelapsePage;
});