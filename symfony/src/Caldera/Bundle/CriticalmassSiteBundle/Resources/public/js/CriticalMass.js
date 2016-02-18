var CriticalMass = CriticalMass || {};

CriticalMass.loadModule = function(name, context, options, callback) {
    var modulePathPrefix = 'js/modules/';
    var type = '';

    if (name.search('Page') > 0) {
        type = 'page/';
    }

    if (name.search('Entity') > 0) {
        type = 'entity/';
    }

    if (name.search('Map') > 0) {
        type = 'map/';
    }

    if (name.search('Marker') > 0) {
        type = 'map/marker/';
    }

    var moduleName = modulePathPrefix + type + name;

    require([moduleName], function(Module) {
        var module = new Module(context, options);

        if (callback) {
            callback(module);
        }
    });
};

require.config({
    baseUrl: '/bundles/calderacriticalmasssite/',
    paths:
    {
        "CityEntity": "/bundles/calderacriticalmasssite/js/modules/entity/CityEntity",
        "WritePost": "/bundles/calderacriticalmasssite/js/modules/WritePost",
        "RideEntity": "/bundles/calderacriticalmasssite/js/modules/entity/RideEntity",
        "NoLocationRideEntity": "/bundles/calderacriticalmasssite/js/modules/entity/NoLocationRideEntity",
        "TrackEntity": "/bundles/calderacriticalmasssite/js/modules/entity/TrackEntity",
        "SubrideEntity": "/bundles/calderacriticalmasssite/js/modules/entity/SubrideEntity",
        "PositionEntity": "/bundles/calderacriticalmasssite/js/modules/entity/PositionEntity",
        "BaseEntity": "/bundles/calderacriticalmasssite/js/modules/entity/BaseEntity",
        "MarkerEntity": "/bundles/calderacriticalmasssite/js/modules/entity/MarkerEntity",
        "PhotoEntity": "/bundles/calderacriticalmasssite/js/modules/entity/PhotoEntity",
        "Container": "/bundles/calderacriticalmasssite/js/modules/entity/Container",
        "EditCityPage": "/bundles/calderacriticalmasssite/js/modules/page/EditCityPage",
        "EditRidePage": "/bundles/calderacriticalmasssite/js/modules/page/EditRidePage",
        "LivePage": "/bundles/calderacriticalmasssite/js/modules/page/LivePage",
        "RegionPage": "/bundles/calderacriticalmasssite/js/modules/page/RegionPage",
        "RidePage": "/bundles/calderacriticalmasssite/js/modules/page/RidePage",
        "PhotoViewModal": "/bundles/calderacriticalmasssite/js/modules/PhotoViewModal",
        "Timelapse": "/bundles/calderacriticalmasssite/js/modules/map/Timelapse",
        "TrackRangePage": "/bundles/calderacriticalmasssite/js/modules/page/TrackRangePage",
        "TrackViewPage": "/bundles/calderacriticalmasssite/js/modules/page/TrackViewPage",
        "ViewPhotoPage": "/bundles/calderacriticalmasssite/js/modules/page/ViewPhotoPage",
        "UploadPhotoPage": "/bundles/calderacriticalmasssite/js/modules/page/UploadPhotoPage",
        "Map": "/bundles/calderacriticalmasssite/js/modules/map/Map",
        "Geocoding": "/bundles/calderacriticalmasssite/js/modules/Geocoding",
        "MapLayerControl": "/bundles/calderacriticalmasssite/js/modules/map/MapLayerControl",
        "MapLocationControl": "/bundles/calderacriticalmasssite/js/modules/map/MapLocationControl",
        "MapPositions": "/bundles/calderacriticalmasssite/js/modules/map/MapPositions",
        "Marker": "/bundles/calderacriticalmasssite/js/modules/map/marker/Marker",
        "CityMarker": "/bundles/calderacriticalmasssite/js/modules/map/marker/CityMarker",
        "LocationMarker": "/bundles/calderacriticalmasssite/js/modules/map/marker/LocationMarker",
        "PhotoMarker": "/bundles/calderacriticalmasssite/js/modules/map/marker/PhotoMarker",
        "PositionMarker": "/bundles/calderacriticalmasssite/js/modules/map/marker/PositionMarker",
        "SubrideMarker": "/bundles/calderacriticalmasssite/js/modules/map/marker/SubrideMarker",
        "SnapablePhotoMarker": "/bundles/calderacriticalmasssite/js/modules/map/marker/SnapablePhotoMarker",
        "leaflet": "/bundles/calderacriticalmasssite/js/external/leaflet/leaflet",
        "leaflet-activearea": "/bundles/calderacriticalmasssite/js/external/leaflet/L.activearea",
        "leaflet-locate": "/bundles/calderacriticalmasssite/js/external/leaflet/L.Control.Locate",
        "leaflet-sidebar": "/bundles/calderacriticalmasssite/js/external/leaflet/L.Control.Sidebar",
        "leaflet-geometry": "/bundles/calderacriticalmasssite/js/external/leaflet/leaflet.geometryutil",
        "leaflet-groupedlayer": "/bundles/calderacriticalmasssite/js/external/leaflet/leaflet.groupedlayercontrol",
        "leaflet-snap": "/bundles/calderacriticalmasssite/js/external/leaflet/leaflet.snap",
        "leaflet-hash": "/bundles/calderacriticalmasssite/js/external/leaflet/leaflet-hash",
        "bootstrap-slider": "/bundles/calderacriticalmasssite/js/external/bootstrap/bootstrap-slider",
        "dropzone": "/bundles/calderacriticalmasssite/js/external/dropzone/dropzone.min",
        "typeahead": "/bundles/calderacriticalmasssite/js/external/typeahead/typeahead",
        "bloodhound": "/bundles/calderacriticalmasssite/js/external/typeahead/bloodhound",
        "jquery": "/bundles/calderacriticalmasssite/js/external/jquery/jquery-2.1.4.min",
        "dateformat": "/bundles/calderacriticalmasssite/js/external/dateformat/dateformat"
    },
    shim: {
        'leaflet-locate': {
            deps: ['leaflet'],
            exports: 'L.Control.Locate'
        },
        'leaflet-groupedlayer': {
            deps: ['leaflet'],
            exports: 'L.Control.GroupedLayers'
        },
        'leaflet-snap': {
            deps: ['leaflet'],
            exports: 'L.Handler.MarkerSnap'
        },
        'leaflet-hash': {
            deps: ['leaflet'],
            exports: 'L.Hash'
        },
        typeahead:{
            deps: ['jquery'],
            init: function ($) {
                return require.s.contexts._.registry['typeahead.js'].factory( $ );
            }
        },
        bloodhound: {
            deps: [],
            exports: 'Bloodhound'
        }
    }
});
