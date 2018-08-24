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
    baseUrl: '/',
    paths: {
        "CityEntity": "/js/modules/entity/CityEntity",
        "WritePost": "/js/modules/WritePost",
        "CriticalService": "/js/modules/CriticalService",
        "RideEntity": "/js/modules/entity/RideEntity",
        "Factory": "/js/modules/entity/Factory",
        "NoLocationRideEntity": "/js/modules/entity/NoLocationRideEntity",
        "TrackEntity": "/js/modules/entity/TrackEntity",
        "TimelapseTrackEntity": "/js/modules/entity/TimelapseTrackEntity",
        "SubrideEntity": "/js/modules/entity/SubrideEntity",
        "PositionEntity": "/js/modules/entity/PositionEntity",
        "UserEntity": "/js/modules/entity/UserEntity",
        "BaseEntity": "/js/modules/entity/BaseEntity",
        "MarkerEntity": "/js/modules/entity/MarkerEntity",
        "PhotoEntity": "/js/modules/entity/PhotoEntity",
        "Container": "/js/modules/entity/Container",
        "ClusterContainer": "/js/modules/entity/ClusterContainer",
        "EditCityPage": "/js/modules/page/EditCityPage",
        "EditCityCyclePage": "/js/modules/page/EditCityCyclePage",
        "EditRidePage": "/js/modules/page/EditRidePage",
        "RegionPage": "/js/modules/page/RegionPage",
        "StravaImportPage": "/js/modules/page/StravaImportPage",
        "CalendarPage": "/js/modules/page/CalendarPage",
        "TrackListPage": "/js/modules/page/TrackListPage",
        "RidePage": "/js/modules/page/RidePage",
        "RideStatisticPage": "/js/modules/page/RideStatisticPage",
        "PlacePhotoPage": "/js/modules/page/PlacePhotoPage",
        "PhotoViewModal": "/js/modules/PhotoViewModal",
        "ProfileColorPage": "/js/modules/page/ProfileColorPage",
        "Timelapse": "/js/modules/map/Timelapse",
        "TrackRangePage": "/js/modules/page/TrackRangePage",
        "TrackUploadPage": "/js/modules/page/TrackUploadPage",
        "TrackViewPage": "/js/modules/page/TrackViewPage",
        "TrackDrawPage": "/js/modules/page/TrackDrawPage",
        "ViewPhotoPage": "/js/modules/page/ViewPhotoPage",
        "UploadPhotoPage": "/js/modules/page/UploadPhotoPage",
        "PhotoCensorPage": "/js/modules/page/PhotoCensorPage",
        "CityStatisticPage": "/js/modules/page/CityStatisticPage",
        "EditSubridePage": "/js/modules/page/EditSubridePage",
        "FacebookStatisticPage": "/js/modules/page/FacebookStatisticPage",
        "StatisticPage": "/js/modules/page/StatisticPage",
        "SortableTable": "/js/modules/SortableTable",
        "Map": "/js/modules/map/Map",
        "AutoMap": "/js/modules/map/AutoMap",
        "DrawMap": "/js/modules/map/DrawMap",
        "Geocoding": "/js/modules/Geocoding",
        "HintModal": "/js/modules/HintModal",
        "Modal": "/js/modules/modal/Modal",
        "BaseModalButton": "/js/modules/modal/BaseModalButton",
        "CloseModalButton": "/js/modules/modal/CloseModalButton",
        "ModalButton": "/js/modules/modal/ModalButton",
        "MapLayerControl": "/js/modules/map/MapLayerControl",
        "MapLocationControl": "/js/modules/map/MapLocationControl",
        "MapPositions": "/js/modules/map/MapPositions",
        "Marker": "/js/modules/map/marker/Marker",
        "CityMarker": "/js/modules/map/marker/CityMarker",
        "LocationMarker": "/js/modules/map/marker/LocationMarker",
        "PhotoMarker": "/js/modules/map/marker/PhotoMarker",
        "PositionMarker": "/js/modules/map/marker/PositionMarker",
        "SubrideMarker": "/js/modules/map/marker/SubrideMarker",
        "SnapablePhotoMarker": "/js/modules/map/marker/SnapablePhotoMarker",
        "Search": "/js/modules/Search",
        "Sharing": "/js/modules/Sharing",
        "ReadMore": "/js/modules/ReadMore",
        "CookieNotice": "/js/modules/CookieNotice",
        "readmorejs": "/js/external/readmore/readmore.min",
        "cookie-notice": "/js/external/cookie-notice/cookie-notice",
        "leaflet": "/js/external/leaflet/leaflet",
        "leaflet-activearea": "/js/external/leaflet/L.activearea",
        "leaflet-locate": "/js/external/leaflet/L.Control.Locate",
        "leaflet-sidebar": "/js/external/leaflet/L.Control.Sidebar",
        "leaflet-geometry": "/js/external/leaflet/leaflet.geometryutil",
        "leaflet-groupedlayer": "/js/external/leaflet/leaflet.groupedlayercontrol",
        "leaflet-snap": "/js/external/leaflet/leaflet.snap",
        "leaflet-hash": "/js/external/leaflet/leaflet-hash",
        "leaflet-sleep": "/js/external/leaflet/leaflet.sleep",
        "leaflet-polyline": "/js/external/leaflet/leaflet-polyline",
        "leaflet-extramarkers": "/js/external/leaflet/ExtraMarkers",
        "leaflet-markercluster": "/js/external/leaflet/leaflet-markercluster",
        "leaflet-draw": "/js/external/leaflet/leaflet.draw",
        "leaflet-routing-draw": "/js/external/leaflet/L.Routing.Draw",
        "leaflet-routing-edit": "/js/external/leaflet/L.Routing.Edit",
        "leaflet-routing": "/js/external/leaflet/L.Routing",
        "leaflet-routing-storage": "/js/external/leaflet/L.Routing.Storage",
        "leaflet-snapping-lineutil": "/js/external/leaflet/LineUtil.Snapping",
        "leaflet-snapping-marker": "/js/external/leaflet/Marker.Snapping",
        "leaflet-snapping-polyline": "/js/external/leaflet/Polyline.Snapping",
        "bootstrap-slider": "/js/external/bootstrap/bootstrap-slider",
        "dropzone": "/js/external/dropzone/dropzone.min",
        "typeahead": "/js/external/typeahead/typeahead",
        "bloodhound": "/js/external/typeahead/bloodhound",
        "jquery": "/js/external/jquery/jquery-3.2.1.min",
        "jquery-areaselect": "/js/external/jquery/jquery.areaselect.min",
        "dateformat": "/js/external/dateformat/dateformat",
        "chartjs": "/js/external/chartjs/chartjs",
        "localforage": "/js/external/localforage/localforage.min",
        "bootstrap-datepicker": "/js/external/bootstrap-datepicker/bootstrap-datepicker.min",
        "bootstrap-colorpicker": "/js/external/bootstrap-colorpicker/bootstrap-colorpicker.min",
        "bootstrap4": "/js/external/bootstrap4/bootstrap.min",
        "bootstrap4app": "/js/external/bootstrap-app-4/toolkit",
        "popper": "/js/external/popper/popper.min",
        "datatables": "/js/external/datatables/datatables.min"
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
