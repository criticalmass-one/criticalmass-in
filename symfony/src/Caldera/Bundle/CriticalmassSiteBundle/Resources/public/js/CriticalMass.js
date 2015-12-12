var CriticalMass = CriticalMass || {};

CriticalMass.loadModule = function(name, context, options) {
    var moduleFile = 'js/modules/' + name;
    
    require([moduleFile], function(Module) {
        new Module(context, options);
    });
};

require.config({
    baseUrl: '/bundles/calderacriticalmasssite/'
});
