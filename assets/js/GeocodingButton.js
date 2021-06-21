import L from "leaflet";

export default class GeocodingButton {
    constructor(geocodingButton, options) {
        const defaults = {};

        this.settings = {...defaults, ...options};

        geocodingButton.addEventListener('click', (event) => {
            const country = 'Deutschland';//geocodingButton.dataset.geocodingCountry;
            const state = 'Hamburg';//geocodingButton.dataset.geocodingState;
            const city = 'Hamburg';//geocodingButton.dataset.geocodingCity;

            const inputSelector = geocodingButton.dataset.geocodingInputSelector;

            const street = document.querySelector(inputSelector).value;

            const nominatimUrl = 'https://nominatim.openstreetmap.org/search?limit=1&polygon_geojson=1&format=jsonv2&q=' + street + ',' + city + ',' + state + ',' + country;
            console.log(nominatimUrl);

            fetch(nominatimUrl).then((response) => {
                return response.json();
            }).then((resultList) => {
                if (resultList.length > 0) {
                    const bestResult = resultList.pop();

                    const event = new CustomEvent('geocoding-result');
                    event.result = bestResult;

                    document.dispatchEvent(event);
                }
            }).catch(function (err) {
                console.warn(err);
            });
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const geocodingButtonList = document.querySelectorAll('.geocode');

    geocodingButtonList.forEach((geocodingButton) => {
        new GeocodingButton(geocodingButton);
    });
});
