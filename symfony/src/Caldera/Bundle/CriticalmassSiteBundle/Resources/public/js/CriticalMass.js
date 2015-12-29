var CriticalMass = CriticalMass || {};

CriticalMass.loadModule = function(name, context, options, callback) {
    var moduleFile = 'js/modules/' + name;

    require([moduleFile], function(Module) {
        var module = new Module(context, options);

        callback(module);
    });
};

require.config({
    baseUrl: '/bundles/calderacriticalmasssite/',
    paths:
    {
        "City": "/bundles/calderacriticalmasssite/js/modules/entity/City",
        "Container": "/bundles/calderacriticalmasssite/js/modules/entity/Container",
        "Map": "/bundles/calderacriticalmasssite/js/modules/map/Map",
        "Marker": "/bundles/calderacriticalmasssite/js/modules/map/marker/Marker",
        "CityMarker": "/bundles/calderacriticalmasssite/js/modules/map/marker/CityMarker",
        "LocationMarker": "/bundles/calderacriticalmasssite/js/modules/map/marker/LocationMarker",
        "leaflet": "/bundles/calderacriticalmasssite/js/external/leaflet/leaflet"
    }
});
