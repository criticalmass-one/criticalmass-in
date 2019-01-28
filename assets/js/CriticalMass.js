var CriticalMass = CriticalMass || {};

CriticalMass.loadModule = function (name, context, options, callback) {
    console.log(name);
    require([name], function (Module) {
        var module = new Module(context, options);

        if (callback) {
            callback(module);
        }
    });
};

require.config({
    baseUrl: '/js/',
    shim: {
        'bootstrap.bundle': {
            deps: ['jquery']
        },
        'leaflet-sleep': {
            deps: ['leaflet'],
            exports: 'L.Map.Sleep'
        },
        'leaflet.extra-markers': {
            deps: ['leaflet'],
            exports: 'L.ExtraMarkers'
        },
        'leaflet.markercluster': {
            deps: ['leaflet'],
            exports: 'L.MarkerClusterGroup'
        },
        'leaflet.groupedlayercontrol': {
            deps: ['leaflet'],
            exports: 'L.Control.GroupedLayers'
        },
        'Polyline.encoded': {
            deps: ['leaflet'],
            exports: 'L.PolylineUtil'
        },
        'dateformat': {
            exports: 'dateFormat'
        },
        'typeahead.jquery': {
            deps: ['jquery'],
            init: function ($) {
                return require.s.contexts._.registry['typeahead.js'].factory($);
            }
        },
        'bloodhound': {
            deps: [],
            exports: 'Bloodhound'
        }
    }
});
