var CriticalMass = CriticalMass || {};

CriticalMass.loadModule = function (name, context, options, callback) {
    require([name], function (Module) {
        var module = new Module(context, options);

        if (callback) {
            callback(module);
        }
    });
};

require.config({
    baseUrl: '/bundles/app/',
    paths: {
        "CityEntity": "/bundles/app/js/modules/entity/CityEntity",
        "WritePost": "/bundles/app/js/modules/WritePost",
        "CriticalService": "/bundles/app/js/modules/CriticalService",
        "RideEntity": "/bundles/app/js/modules/entity/RideEntity",
        "Factory": "/bundles/app/js/modules/entity/Factory",
        "NoLocationRideEntity": "/bundles/app/js/modules/entity/NoLocationRideEntity",
        "TrackEntity": "/bundles/app/js/modules/entity/TrackEntity",
        "TimelapseTrackEntity": "/bundles/app/js/modules/entity/TimelapseTrackEntity",
        "SubrideEntity": "/bundles/app/js/modules/entity/SubrideEntity",
        "PositionEntity": "/bundles/app/js/modules/entity/PositionEntity",
        "UserEntity": "/bundles/app/js/modules/entity/UserEntity",
        "BaseEntity": "/bundles/app/js/modules/entity/BaseEntity",
        "MarkerEntity": "/bundles/app/js/modules/entity/MarkerEntity",
        "PhotoEntity": "/bundles/app/js/modules/entity/PhotoEntity",
        "Container": "/bundles/app/js/modules/entity/Container",
        "ClusterContainer": "/bundles/app/js/modules/entity/ClusterContainer",
        "EditCityPage": "/bundles/app/js/modules/page/EditCityPage",
        "EditCityCyclePage": "/bundles/app/js/modules/page/EditCityCyclePage",
        "EditRidePage": "/bundles/app/js/modules/page/EditRidePage",
        "RegionPage": "/bundles/app/js/modules/page/RegionPage",
        "StravaImportPage": "/bundles/app/js/modules/page/StravaImportPage",
        "CalendarPage": "/bundles/app/js/modules/page/CalendarPage",
        "TrackListPage": "/bundles/app/js/modules/page/TrackListPage",
        "FacebookImportRidePage": "/bundles/app/js/modules/page/FacebookImportRidePage",
        "RidePage": "/bundles/app/js/modules/page/RidePage",
        "RideStatisticPage": "/bundles/app/js/modules/page/RideStatisticPage",
        "PlacePhotoPage": "/bundles/app/js/modules/page/PlacePhotoPage",
        "PhotoViewModal": "/bundles/app/js/modules/PhotoViewModal",
        "Timelapse": "/bundles/app/js/modules/map/Timelapse",
        "TrackRangePage": "/bundles/app/js/modules/page/TrackRangePage",
        "TrackUploadPage": "/bundles/app/js/modules/page/TrackUploadPage",
        "TrackViewPage": "/bundles/app/js/modules/page/TrackViewPage",
        "TrackDrawPage": "/bundles/app/js/modules/page/TrackDrawPage",
        "ViewPhotoPage": "/bundles/app/js/modules/page/ViewPhotoPage",
        "UploadPhotoPage": "/bundles/app/js/modules/page/UploadPhotoPage",
        "PhotoCensorPage": "/bundles/app/js/modules/page/PhotoCensorPage",
        "CityStatisticPage": "/bundles/app/js/modules/page/CityStatisticPage",
        "EditSubridePage": "/bundles/app/js/modules/page/EditSubridePage",
        "FacebookStatisticPage": "/bundles/app/js/modules/page/FacebookStatisticPage",
        "StatisticPage": "/bundles/app/js/modules/page/StatisticPage",
        "SortableTable": "/bundles/app/js/modules/SortableTable",
        "Map": "/bundles/app/js/modules/map/Map",
        "AutoMap": "/bundles/app/js/modules/map/AutoMap",
        "DrawMap": "/bundles/app/js/modules/map/DrawMap",
        "Geocoding": "/bundles/app/js/modules/Geocoding",
        "Modal": "/bundles/app/js/modules/modal/Modal",
        "BaseModalButton": "/bundles/app/js/modules/modal/BaseModalButton",
        "CloseModalButton": "/bundles/app/js/modules/modal/CloseModalButton",
        "ModalButton": "/bundles/app/js/modules/modal/ModalButton",
        "MapLayerControl": "/bundles/app/js/modules/map/MapLayerControl",
        "MapLocationControl": "/bundles/app/js/modules/map/MapLocationControl",
        "MapPositions": "/bundles/app/js/modules/map/MapPositions",
        "Marker": "/bundles/app/js/modules/map/marker/Marker",
        "CityMarker": "/bundles/app/js/modules/map/marker/CityMarker",
        "LocationMarker": "/bundles/app/js/modules/map/marker/LocationMarker",
        "PhotoMarker": "/bundles/app/js/modules/map/marker/PhotoMarker",
        "PositionMarker": "/bundles/app/js/modules/map/marker/PositionMarker",
        "SubrideMarker": "/bundles/app/js/modules/map/marker/SubrideMarker",
        "SnapablePhotoMarker": "/bundles/app/js/modules/map/marker/SnapablePhotoMarker",
        "Search": "/bundles/app/js/modules/Search",
        "ReadMore": "/bundles/app/js/modules/ReadMore",
        "CookieNotice": "/bundles/app/js/modules/CookieNotice",
        "readmorejs": "/bundles/app/js/external/readmore/readmore.min",
        "cookie-notice": "/bundles/app/js/external/cookie-notice/cookie-notice",
        "leaflet": "/bundles/app/js/external/leaflet/leaflet",
        "leaflet-activearea": "/bundles/app/js/external/leaflet/L.activearea",
        "leaflet-locate": "/bundles/app/js/external/leaflet/L.Control.Locate",
        "leaflet-sidebar": "/bundles/app/js/external/leaflet/L.Control.Sidebar",
        "leaflet-geometry": "/bundles/app/js/external/leaflet/leaflet.geometryutil",
        "leaflet-groupedlayer": "/bundles/app/js/external/leaflet/leaflet.groupedlayercontrol",
        "leaflet-snap": "/bundles/app/js/external/leaflet/leaflet.snap",
        "leaflet-hash": "/bundles/app/js/external/leaflet/leaflet-hash",
        "leaflet-sleep": "/bundles/app/js/external/leaflet/leaflet.sleep",
        "leaflet-polyline": "/bundles/app/js/external/leaflet/leaflet-polyline",
        "leaflet-extramarkers": "/bundles/app/js/external/leaflet/ExtraMarkers",
        "leaflet-markercluster": "/bundles/app/js/external/leaflet/leaflet-markercluster",
        "leaflet-draw": "/bundles/app/js/external/leaflet/leaflet.draw",
        "leaflet-routing-draw": "/bundles/app/js/external/leaflet/L.Routing.Draw",
        "leaflet-routing-edit": "/bundles/app/js/external/leaflet/L.Routing.Edit",
        "leaflet-routing": "/bundles/app/js/external/leaflet/L.Routing",
        "leaflet-routing-storage": "/bundles/app/js/external/leaflet/L.Routing.Storage",
        "leaflet-snapping-lineutil": "/bundles/app/js/external/leaflet/LineUtil.Snapping",
        "leaflet-snapping-marker": "/bundles/app/js/external/leaflet/Marker.Snapping",
        "leaflet-snapping-polyline": "/bundles/app/js/external/leaflet/Polyline.Snapping",
        "bootstrap-slider": "/bundles/app/js/external/bootstrap/bootstrap-slider",
        "dropzone": "/bundles/app/js/external/dropzone/dropzone.min",
        "typeahead": "/bundles/app/js/external/typeahead/typeahead",
        "bloodhound": "/bundles/app/js/external/typeahead/bloodhound",
        "jquery": "/bundles/app/js/external/jquery/jquery-3.2.1.min",
        "jquery-areaselect": "/bundles/app/js/external/jquery/jquery.areaselect.min",
        "jquery-tablesorter": "/bundles/app/js/external/jquery/jquery.tablesorter",
        "dateformat": "/bundles/app/js/external/dateformat/dateformat",
        "chartjs": "/bundles/app/js/external/chartjs/chartjs",
        "localforage": "/bundles/app/js/external/localforage/localforage.min",
        "bootstrap-datepicker": "/bundles/app/js/external/bootstrap-datepicker/bootstrap-datepicker.min",
        "bootstrap4": "/bundles/app/js/external/bootstrap4/bootstrap.min",
        "bootstrap4app": "/bundles/app/js/external/bootstrap-app-4/toolkit",
        "popper": "/bundles/app/js/external/popper/popper.min"
    },
    shim: {
        'popper': {
            deps: ['jquery'],
            exports: 'Popper'
        },
        'bootstrap4': {
            deps: ['jquery', 'popper']
        },
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
        'leaflet-sleep': {
            deps: ['leaflet'],
            exports: 'L.Map.Sleep'
        },
        'leaflet-polyline': {
            deps: ['leaflet'],
            exports: 'L.PolylineUtil'
        },
        'leaflet-playback': {
            deps: ['leaflet'],
            exports: 'L.Playback'
        },
        'leaflet-extramarkers': {
            deps: ['leaflet'],
            exports: 'L.ExtraMarkers'
        },
        'leaflet-markercluster': {
            deps: ['leaflet'],
            exports: 'L.MarkerClusterGroup'
        },
        'leaflet-draw': {
            deps: ['leaflet'],
            exports: 'L.Control.Draw'
        },
        'leaflet-routing': {
            deps: ['leaflet'],
            exports: 'L.Routing'
        },
        'leaflet-routing-draw': {
            deps: ['leaflet', 'leaflet-routing'],
            exports: 'L.Routing.Draw'
        },
        'leaflet-routing-edit': {
            deps: ['leaflet', 'leaflet-routing'],
            exports: 'L.Routing.Edit'
        },
        'leaflet-routing-storage': {
            deps: ['leaflet', 'leaflet-routing'],
            exports: 'L.Routing.Storage'
        },
        'leaflet-snapping-marker': {
            deps: ['leaflet'],
            exports: 'L.Marker'
        },
        'leaflet-snapping-lineutil': {
            deps: ['leaflet'],
            exports: 'L.LineUtil'
        },
        'leaflet-snapping-polyline': {
            deps: ['leaflet'],
            exports: 'L.Polyline'
        },
        typeahead: {
            deps: ['jquery'],
            init: function ($) {
                return require.s.contexts._.registry['typeahead.js'].factory($);
            }
        },
        bloodhound: {
            deps: [],
            exports: 'Bloodhound'
        }
    }
});

define('initBootstrap', ['popper'], function(popper) {
    // set popper as required by Bootstrap
    window.Popper = popper;
    require(['bootstrap4app'], function(bootstrap) {
    });
});
