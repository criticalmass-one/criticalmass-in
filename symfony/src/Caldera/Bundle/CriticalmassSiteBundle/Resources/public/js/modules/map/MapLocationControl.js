define(['leaflet', 'leaflet-locate'], function(L) {
    MapLocationControl = function() {

    };

    MapLocationControl.prototype._locateControl = null;

    MapLocationControl.prototype.init = function() {
        this._locateControl = L.control.locate({
            position: "bottomright",
            drawCircle: true,
            follow: true,
            setView: true,
            keepCurrentZoomLevel: true,
            markerStyle: {
                weight: 1,
                opacity: 0.8,
                fillOpacity: 0.8
            },
            circleStyle: {
                weight: 1,
                clickable: false
            },
            icon: "fa fa-location-arrow",
            metric: false,
            strings: {
                title: "My location",
                popup: "You are within {distance} {unit} from this point",
                outsideMapBoundsMsg: "You seem located outside the boundaries of the map"
            },
            locateOptions: {
                maxZoom: 18,
                watch: true,
                enableHighAccuracy: true,
                maximumAge: 10000,
                timeout: 10000
            }
        });
    };

    MapLocationControl.prototype.addTo = function(map) {
        this._locateControl.addTo(map.map);
    };

    return MapLocationControl;
});