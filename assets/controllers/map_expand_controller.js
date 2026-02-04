import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['container', 'icon'];

    toggle() {
        const isExpanded = this.containerTarget.classList.toggle('map-expanded');

        // Icon wechseln
        if (this.hasIconTarget) {
            this.iconTarget.classList.toggle('fa-expand', !isExpanded);
            this.iconTarget.classList.toggle('fa-compress', isExpanded);
        }

        // Window resize Event dispatchen, damit Leaflet sich neu zeichnet
        setTimeout(() => {
            window.dispatchEvent(new Event('resize'));
        }, 350);
    }
}
