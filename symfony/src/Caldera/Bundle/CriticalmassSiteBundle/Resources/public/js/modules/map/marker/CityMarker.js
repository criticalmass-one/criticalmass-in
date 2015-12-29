define(['Marker'], function() {
    CityMarker = function (latLng, draggable) {
        this._latLng = latLng;
        this._draggable = draggable;

        this._icon = L.icon({
            iconUrl: '/images/marker/marker-red.png',
            iconRetinaUrl: '/images/marker/marker-red-2x.png',
            iconSize: [25, 41],
            iconAnchor: [13, 41],
            popupAnchor: [0, -36],
            shadowUrl: '/images/marker/defaultshadow.png',
            shadowSize: [41, 41],
            shadowAnchor: [13, 41]
        });
    };

    CityMarker.prototype = new Marker();
    CityMarker.prototype.constructor = CityMarker;

    return CityMarker;
});