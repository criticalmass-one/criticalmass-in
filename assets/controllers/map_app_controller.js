import { Controller } from '@hotwired/stimulus';

// Auf dem Handy oeffnet der Link die Karten-App; sonst bleibt die
// OpenStreetMap-Adresse aus dem Markup.
export default class extends Controller {
    static values = { latitude: Number, longitude: Number, name: String };

    connect() {
        const ua = navigator.userAgent;
        const ios = /iPhone|iPad|iPod/.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
        const point = `${this.latitudeValue},${this.longitudeValue}`;
        const name = encodeURIComponent(this.nameValue || point);

        if (ios) {
            this.element.href = `maps://?ll=${point}&q=${name}`;
        } else if (/Android/.test(ua)) {
            this.element.href = `geo:${point}?q=${point}(${name})`;
        }
    }
}
