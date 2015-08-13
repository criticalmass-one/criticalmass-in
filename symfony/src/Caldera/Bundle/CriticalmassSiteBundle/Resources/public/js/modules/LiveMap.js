define(['LiveMap'], function() {
        var LiveMap = function(context, settings) {
            this.context = context;
            this.settings = settings;
        };

        LiveMap.prototype.$$init = function() {
            alert('foo');
        }
    }
);