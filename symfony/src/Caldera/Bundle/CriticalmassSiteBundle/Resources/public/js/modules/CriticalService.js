define([], function() {
    var instance = null;

    function CriticalService(){
        if(instance !== null){
            throw new Error("Cannot instantiate more than one MySingleton, use MySingleton.getInstance()");
        }

        this.initialize();
    }

    CriticalService.prototype = {
        initialize: function(){

        },

        setMap: function(map) {
            this._map = map;
        },

        getMap: function() {
            return this._map;
        }
    };
    CriticalService.getInstance = function(){
        // summary:
        //      Gets an instance of the singleton. It is better to use
        if(instance === null){
            instance = new CriticalService();
        }
        return instance;
    };

    return CriticalService.getInstance();
});
