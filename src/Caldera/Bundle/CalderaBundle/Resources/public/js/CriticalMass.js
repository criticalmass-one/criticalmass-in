var CriticalMass = CriticalMass || {};

CriticalMass.loadModule = function(name, context, options, callback) {
    require([name], function(Module) {
        var module = new Module(context, options);

        if (callback) {
            callback(module);
        }
    });
};

require.config({
    baseUrl: '/bundles/caldera/',
    paths:
    {
        "CalderaCityMapPage": "/bundles/caldera/js/modules/page/CalderaCityMapPage",
        "CyclewaysIncidentPage": "/bundles/calderacycleways/js/modules/page/CyclewaysIncidentPage",
        "CyclewaysIncidentEditPage": "/bundles/calderacycleways/js/modules/page/CyclewaysIncidentEditPage",
        "CyclewaysIncidentShowPage": "/bundles/calderacycleways/js/modules/page/CyclewaysIncidentShowPage",
        "CyclewaysLocationSearch": "/bundles/calderacycleways/js/modules/CyclewaysLocationSearch",
        "CityEntity": "/bundles/caldera/js/modules/entity/CityEntity",
        "WritePost": "/bundles/caldera/js/modules/WritePost",
        "CriticalService": "/bundles/caldera/js/modules/CriticalService",
        "RideEntity": "/bundles/caldera/js/modules/entity/RideEntity",
        "EventEntity": "/bundles/caldera/js/modules/entity/EventEntity",
        "LiveRideEntity": "/bundles/caldera/js/modules/entity/LiveRideEntity",
        "Factory": "/bundles/caldera/js/modules/entity/Factory",
        "NoLocationRideEntity": "/bundles/caldera/js/modules/entity/NoLocationRideEntity",
        "TrackEntity": "/bundles/caldera/js/modules/entity/TrackEntity",
        "TimelapseTrackEntity": "/bundles/caldera/js/modules/entity/TimelapseTrackEntity",
        "SubrideEntity": "/bundles/caldera/js/modules/entity/SubrideEntity",
        "PositionEntity": "/bundles/caldera/js/modules/entity/PositionEntity",
        "IncidentEntity": "/bundles/caldera/js/modules/entity/IncidentEntity",
        "UserEntity": "/bundles/caldera/js/modules/entity/UserEntity",
        "BaseEntity": "/bundles/caldera/js/modules/entity/BaseEntity",
        "MarkerEntity": "/bundles/caldera/js/modules/entity/MarkerEntity",
        "PhotoEntity": "/bundles/caldera/js/modules/entity/PhotoEntity",
        "Container": "/bundles/caldera/js/modules/entity/Container",
        "LocalContainer": "/bundles/caldera/js/modules/entity/LocalContainer",
        "ClusterContainer": "/bundles/caldera/js/modules/entity/ClusterContainer",
        "IncidentContainer": "/bundles/caldera/js/modules/entity/IncidentContainer",
        "EditCityPage": "/bundles/caldera/js/modules/page/EditCityPage",
        "EditRidePage": "/bundles/caldera/js/modules/page/EditRidePage",
        "LivePage": "/bundles/caldera/js/modules/page/LivePage",
        "LiveFrontPage": "/bundles/caldera/js/modules/page/LiveFrontPage",
        "RegionPage": "/bundles/caldera/js/modules/page/RegionPage",
        "StravaImportPage": "/bundles/caldera/js/modules/page/StravaImportPage",
        "TrackListPage": "/bundles/caldera/js/modules/page/TrackListPage",
        "FacebookImportRidePage": "/bundles/caldera/js/modules/page/FacebookImportRidePage",
        "RidePage": "/bundles/caldera/js/modules/page/RidePage",
        "RideStatisticPage": "/bundles/caldera/js/modules/page/RideStatisticPage",
        "IncidentEditPage": "/bundles/caldera/js/modules/page/IncidentEditPage",
        "PhotoViewModal": "/bundles/caldera/js/modules/PhotoViewModal",
        "Notification": "/bundles/caldera/js/modules/Notification",
        "Timelapse": "/bundles/caldera/js/modules/map/Timelapse",
        "TrackRangePage": "/bundles/caldera/js/modules/page/TrackRangePage",
        "TrackUploadPage": "/bundles/caldera/js/modules/page/TrackUploadPage",
        "TrackViewPage": "/bundles/caldera/js/modules/page/TrackViewPage",
        "TrackDrawPage": "/bundles/caldera/js/modules/page/TrackDrawPage",
        "ViewPhotoPage": "/bundles/caldera/js/modules/page/ViewPhotoPage",
        "UploadPhotoPage": "/bundles/caldera/js/modules/page/UploadPhotoPage",
        "ChatPage": "/bundles/caldera/js/modules/page/ChatPage",
        "CityStatisticPage": "/bundles/caldera/js/modules/page/CityStatisticPage",
        "EditSubridePage": "/bundles/caldera/js/modules/page/EditSubridePage",
        "FacebookStatisticPage": "/bundles/caldera/js/modules/page/FacebookStatisticPage",
        "StatisticPage": "/bundles/caldera/js/modules/page/StatisticPage",
        "Map": "/bundles/caldera/js/modules/map/Map",
        "AutoMap": "/bundles/caldera/js/modules/map/AutoMap",
        "DrawMap": "/bundles/caldera/js/modules/map/DrawMap",
        "Geocoding": "/bundles/caldera/js/modules/Geocoding",
        "Modal": "/bundles/caldera/js/modules/modal/Modal",
        "BaseModalButton": "/bundles/caldera/js/modules/modal/BaseModalButton",
        "CloseModalButton": "/bundles/caldera/js/modules/modal/CloseModalButton",
        "ModalButton": "/bundles/caldera/js/modules/modal/ModalButton",
        "MapLayerControl": "/bundles/caldera/js/modules/map/MapLayerControl",
        "MapLocationControl": "/bundles/caldera/js/modules/map/MapLocationControl",
        "MapPositions": "/bundles/caldera/js/modules/map/MapPositions",
        "Marker": "/bundles/caldera/js/modules/map/marker/Marker",
        "CityMarker": "/bundles/caldera/js/modules/map/marker/CityMarker",
        "LocationMarker": "/bundles/caldera/js/modules/map/marker/LocationMarker",
        "IncidentMarker": "/bundles/caldera/js/modules/map/marker/IncidentMarker",
        "PhotoMarker": "/bundles/caldera/js/modules/map/marker/PhotoMarker",
        "PositionMarker": "/bundles/caldera/js/modules/map/marker/PositionMarker",
        "SubrideMarker": "/bundles/caldera/js/modules/map/marker/SubrideMarker",
        "SnapablePhotoMarker": "/bundles/caldera/js/modules/map/marker/SnapablePhotoMarker",
        "IncidentMarkerIcon": "/bundles/caldera/js/modules/map/icon/IncidentMarkerIcon",
        "Search": "/bundles/caldera/js/modules/Search",
        "localforage": "/bundles/caldera/js/external/localforage/localforage-1.4.3",
        "leaflet": "/bundles/caldera/js/external/leaflet/leaflet",
        "leaflet-activearea": "/bundles/caldera/js/external/leaflet/L.activearea",
        "leaflet-locate": "/bundles/caldera/js/external/leaflet/L.Control.Locate",
        "leaflet-sidebar": "/bundles/caldera/js/external/leaflet/L.Control.Sidebar",
        "leaflet-geometry": "/bundles/caldera/js/external/leaflet/leaflet.geometryutil",
        "leaflet-groupedlayer": "/bundles/caldera/js/external/leaflet/leaflet.groupedlayercontrol",
        "leaflet-snap": "/bundles/caldera/js/external/leaflet/leaflet.snap",
        "leaflet-hash": "/bundles/caldera/js/external/leaflet/leaflet-hash",
        "leaflet-polyline": "/bundles/caldera/js/external/leaflet/leaflet-polyline",
        "leaflet-extramarkers": "/bundles/caldera/js/external/leaflet/ExtraMarkers",
        "leaflet-markercluster": "/bundles/caldera/js/external/leaflet/leaflet-markercluster",
        "leaflet-draw": "/bundles/caldera/js/external/leaflet/leaflet.draw",
        "leaflet-routing-draw": "/bundles/caldera/js/external/leaflet/L.Routing.Draw",
        "leaflet-routing-edit": "/bundles/caldera/js/external/leaflet/L.Routing.Edit",
        "leaflet-routing": "/bundles/caldera/js/external/leaflet/L.Routing",
        "leaflet-routing-storage": "/bundles/caldera/js/external/leaflet/L.Routing.Storage",
        "leaflet-snapping-lineutil": "/bundles/caldera/js/external/leaflet/LineUtil.Snapping",
        "leaflet-snapping-marker": "/bundles/caldera/js/external/leaflet/Marker.Snapping",
        "leaflet-snapping-polyline": "/bundles/caldera/js/external/leaflet/Polyline.Snapping",
        "bootstrap-slider": "/bundles/caldera/js/external/bootstrap/bootstrap-slider",
        "dropzone": "/bundles/caldera/js/external/dropzone/dropzone.min",
        "typeahead": "/bundles/caldera/js/external/typeahead/typeahead",
        "bloodhound": "/bundles/caldera/js/external/typeahead/bloodhound",
        "jquery": "/bundles/caldera/js/external/jquery/jquery-2.1.4.min",
        "dateformat": "/bundles/caldera/js/external/dateformat/dateformat",
        "socketio": "/bundles/caldera/js/external/socketio/socketio",
        "chartjs": "/bundles/caldera/js/external/chartjs/chartjs",
        "hammerjs": "/bundles/caldera/js/external/hammerjs/hammer.min"
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
        'socketio': {
            exports: 'io'
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
