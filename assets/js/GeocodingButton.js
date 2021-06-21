import L from "leaflet";

export default class GeocodingButton {
    constructor(geocodingButton, options) {
        const defaults = {};

        this.settings = {...defaults, ...options};

        geocodingButton.addEventListener('click', (event) => {
            const queryParts = [];

            let query = geocodingButton.dataset.geocodingQuery;
            const geocodingQuerySelector = geocodingButton.dataset.geocodingQuerySelector;

            if (!query && geocodingQuerySelector) {
                const geocodingQueryElement = document.querySelector(geocodingQuerySelector);

                if ('INPUT' === geocodingQueryElement.tagName) {
                    query = geocodingQueryElement.value;
                }

                if ('SELECT' === geocodingQueryElement.tagName) {
                    query = geocodingQueryElement.selectedOptions[0].text;
                }
            }

            if (query) {
                queryParts.push(query);
            }

            let querySuffix = geocodingButton.dataset.geocodingQuerySuffix;
            const geocodingQuerySuffixSelector = geocodingButton.dataset.geocodingQuerySuffixSelector;

            if (!querySuffix && geocodingQuerySuffixSelector) {
                const geocodingQuerySuffixElement = document.querySelector(geocodingQuerySuffixSelector);

                if ('INPUT' === geocodingQuerySuffixElement.tagName) {
                    querySuffix = geocodingQuerySuffixElement.value;
                }

                if ('SELECT' === geocodingQuerySuffixElement.tagName) {
                    querySuffix = geocodingQuerySuffixElement.selectedOptions[0].text;
                }
            }

            if (querySuffix) {
                queryParts.push(querySuffix);
            }

            let queryPart = geocodingButton.dataset.geocodingQueryPart;
            const geocodingQueryPartSelector = geocodingButton.dataset.geocodingQueryPartSelector;

            if (!queryPart && geocodingQueryPartSelector) {
                const geocodingQueryPartElement = document.querySelector(geocodingQueryPartSelector);

                if ('INPUT' === geocodingQueryPartElement.tagName) {
                    queryPart = geocodingQueryPartElement.value;
                }

                if ('SELECT' === geocodingQueryPartElement.tagName) {
                    queryPart = geocodingQueryPartElement.selectedOptions[0].text;
                }
            }

            if (queryPart) {
                queryParts.push(queryPart);
            }

            let nominatimUrl = 'https://nominatim.openstreetmap.org/search?limit=1&polygon_geojson=1&format=jsonv2&q=' + queryParts.join(',');

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
