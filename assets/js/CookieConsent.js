export default class Calendar {
    constructor(options) {
        const defaults = {};

        this.settings = {...defaults, ...options};

        this.init();
    }

    init() {
        window.cookieconsent.initialise({
            'palette': {
                'popup': {
                    'background': '#64386b',
                    'text': '#ffcdfd'
                },
                'button': {
                    'background': '#f8a8ff',
                    'text': '#3f0045'
                }
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new CookieConsent();
});
