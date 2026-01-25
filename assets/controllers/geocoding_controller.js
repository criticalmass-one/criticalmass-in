import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        query: String,
        querySelector: String,
        querySuffix: String,
        querySuffixSelector: String,
        queryPart: String,
        queryPartSelector: String
    }

    search(event) {
        event.preventDefault();

        const queryParts = [];

        let query = this.getValueFromSelectorOrValue(this.queryValue, this.querySelectorValue);
        if (query) {
            queryParts.push(query);
        }

        let querySuffix = this.getValueFromSelectorOrValue(this.querySuffixValue, this.querySuffixSelectorValue);
        if (querySuffix) {
            queryParts.push(querySuffix);
        }

        let queryPart = this.getValueFromSelectorOrValue(this.queryPartValue, this.queryPartSelectorValue);
        if (queryPart) {
            queryParts.push(queryPart);
        }

        const nominatimUrl = 'https://nominatim.openstreetmap.org/search?limit=1&polygon_geojson=1&format=jsonv2&q=' + queryParts.join(',');

        fetch(nominatimUrl)
            .then(response => response.json())
            .then(resultList => {
                if (resultList.length > 0) {
                    const bestResult = resultList.pop();

                    const event = new CustomEvent('geocoding-result', { detail: bestResult });
                    document.dispatchEvent(event);
                }
            })
            .catch(err => console.warn(err));
    }

    getValueFromSelectorOrValue(value, selector) {
        if (value) {
            return value;
        }

        if (!selector) {
            return null;
        }

        const element = document.querySelector(selector);
        if (!element) {
            return null;
        }

        if (element.tagName === 'INPUT') {
            return element.value;
        }

        if (element.tagName === 'SELECT') {
            return element.selectedOptions[0]?.text;
        }

        return null;
    }
}
