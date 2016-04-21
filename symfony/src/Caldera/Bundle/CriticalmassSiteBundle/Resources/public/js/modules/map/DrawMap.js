define(['Map', 'leaflet-draw'], function() {
    DrawMap = function (mapId, settings) {
        this._mapId = mapId;

        this.settings = $.extend(this._defaults, settings);

        this._init();
    };

    // do not call Map constructor directly as this will execute the map
    DrawMap.prototype = Object.create(Map.prototype);
    DrawMap.prototype.constructor = DrawMap;

    DrawMap.prototype._loadStyles = function() {
        var $link = $('<link>', {
            rel: 'stylesheet',
            type: 'text/css',
            href: this.settings.stylesheetAddress
        });

        $link.appendTo('head');

        $link = $('<link>', {
            rel: 'stylesheet',
            type: 'text/css',
            href: '/bundles/calderacriticalmasssite/css/external/leaflet.draw.css'
        });

        $link.appendTo('head');
    };

    DrawMap.prototype._init = function () {
        this._loadStyles();
        this._initMap();
        this._addTileLayer();
    };

    return DrawMap;
});
