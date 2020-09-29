import L from "leaflet";

export default class GeocodingButton {
    constructor(element, options) {
        const defaults = {};

        this.settings = {...defaults, ...options};

        this.init();
    }

    init() {
        const geocodingButton = document.querySelector('.geocoding');

        if (geocodingButton) {
            geocodingButton.addEventListener('click', function () {
                const apiRequest = new XMLHttpRequest();

                apiRequest.onreadystatechange = function () {
                    if (apiRequest.readyState === 4) {
                        if (apiRequest.status === 200) {
                            const mapContainer = document.getElementById('map');
                            const markerNumber = parseInt(geocodingButton.dataset.targetMarkerNumber);

                            const nominatimResponse = JSON.parse(apiRequest.responseText);

                            if (nominatimResponse.length === 1) {
                                const osmPlace = nominatimResponse.pop();

                                const markerLatLng = L.latLng(osmPlace.lat, osmPlace.lon);
                                console.log(markerLatLng);

                                mapContainer.map.eachLayer(function (layer) {
                                    if (layer.markerNumber !== undefined && layer.markerNumber === markerNumber) {
                                        layer.setLatLng(markerLatLng);

                                        const northWest = L.latLng(osmPlace.boundingbox[0], osmPlace.boundingbox[2]);
                                        const southEast = L.latLng(osmPlace.boundingbox[1], osmPlace.boundingbox[3]);

                                        const boundingBox = L.latLngBounds(northWest, southEast);

                                        //mapContainer.map.setView(markerLatLng, 10);
                                        mapContainer.map.fitBounds(boundingBox);
                                    }
                                });
                            }
                        }
                    }
                }

                const country = geocodingButton.dataset.geocodingCountry;
                const state = geocodingButton.dataset.geocodingState;
                const city = geocodingButton.dataset.geocodingCity;

                const inputSelector = geocodingButton.dataset.geocodingInputSelector;

                const street = document.querySelector(inputSelector).value;

                const nominatimUrl = 'https://nominatim.openstreetmap.org/search?limit=1&polygon_geojson=1&format=jsonv2&q=' + street + ',' + city + ',' + state + ',' + country;
                console.log(nominatimUrl);

                apiRequest.open('Get', nominatimUrl);
                apiRequest.send();
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const element = document.querySelector('.geocoding');

    new GeocodingButton(element);
});
