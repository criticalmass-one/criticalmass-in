export default class DemoDing {
    constructor($element, options) {
        const defaults = {};

        this.settings = {...defaults, ...options};
    }

    fooMethode() {

    }

    barMethode() {

    }
}

document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('#foobarselector');

    new DemoDing(page);
});