// controllers/base_map_controller.js
import { Controller } from '@hotwired/stimulus';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Fix für Leaflet-Icons bei Webpack/Encore
import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
import iconUrl from 'leaflet/dist/images/marker-icon.png';
import shadowUrl from 'leaflet/dist/images/marker-shadow.png';
L.Icon.Default.mergeOptions({ iconRetinaUrl, iconUrl, shadowUrl });

export default class extends Controller {
    static values = {
        centerLatitude: Number,
        centerLongitude: Number,
        zoom: { type: Number, default: 12 },
        maptilerKey: String
    };

    connect() {
        this.initMap();
    }

    initMap() {
        this.map = L.map(this.element, {
            zoomControl: true
        });

        // 1. Map zentrieren
        const lat = this.hasCenterLatitudeValue ? this.centerLatitudeValue : 51.1657;
        const lng = this.hasCenterLongitudeValue ? this.centerLongitudeValue : 10.4515;
        const zoom = this.zoomValue ?? 12;
        this.map.setView([lat, lng], zoom);

        // 2. MapTiler-Layer
        // Key: entweder aus data-Attribut oder dein fixer Key
        const key = this.hasMaptilerKeyValue ? this.maptilerKeyValue : '1jtZ0vdO3g9JKCOlepnM';

        L.tileLayer(
            `https://api.maptiler.com/maps/streets/{z}/{x}/{y}.png?key=${key}`,
            {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> contributors &copy; <a href="https://www.maptiler.com/">MapTiler</a>',
                tileSize: 512,
                zoomOffset: -1,
                maxZoom: 19
            }
        ).addTo(this.map);

        // falls Container anfangs unsichtbar war
        setTimeout(() => this.map.invalidateSize(), 80);
    }

    /**
     * kleine Helper, damit die Subklassen nicht dauernd Leaflet tippen müssen
     */
    createMarker(lat, lng, options = {}) {
        return L.marker([lat, lng], options).addTo(this.map);
    }

    createFeatureGroup() {
        return L.featureGroup().addTo(this.map);
    }

    fitTo(layer) {
        const bounds = layer.getBounds ? layer.getBounds() : null;
        if (bounds && bounds.isValid()) {
            this.map.fitBounds(bounds);
        }
    }

    async loadJson(url) {
        const res = await fetch(url);
        if (!res.ok) {
            throw new Error('HTTP ' + res.status);
        }
        return res.json();
    }

    normalizeColor(input) {
        if (!input) return null;
        const c = String(input).trim();

        // rgb(…, …, …)
        if (/^rgb\s*\(/i.test(c)) {
            const m = c.match(/rgb\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/i);
            if (!m) return c;
            const toHex = (v) => {
                const n = Math.max(0, Math.min(255, parseInt(v, 10)));
                return n.toString(16).padStart(2, '0');
            };
            return `#${toHex(m[1])}${toHex(m[2])}${toHex(m[3])}`;
        }

        // bereits Hex?
        if (c.startsWith('#')) return c;

        // nacktes Hex
        if (/^[0-9a-fA-F]{6}$/.test(c)) return `#${c}`;
        if (/^[0-9a-fA-F]{3}$/.test(c)) return `#${c}`;

        // Farbnamen o. ä. durchreichen
        return c;
    }

    disableInteraction() {
        if (!this.map) return;
        this.map.dragging.disable();
        this.map.touchZoom.disable();
        this.map.doubleClickZoom.disable();
        this.map.scrollWheelZoom.disable();
        this.map.boxZoom.disable();
        this.map.keyboard.disable();
        // Zoom-Control entfernen, wenn vorhanden
        if (this.map.zoomControl) this.map.removeControl(this.map.zoomControl);
        // Cursor neutral, CSS-Hinweis
        this.element.style.cursor = 'default';
        this.element.classList.add('leaflet-interactions-disabled');
        this._interactionDisabled = true;
    }

    enableInteraction() {
        if (!this.map) return;
        this.map.dragging.enable();
        this.map.touchZoom.enable();
        this.map.doubleClickZoom.enable();
        this.map.scrollWheelZoom.enable();
        this.map.boxZoom.enable();
        this.map.keyboard.enable();
        // Zoom-Control wieder hinzufügen
        if (!this.map.zoomControl) {
            this.map.zoomControl = L.control.zoom().addTo(this.map);
        }
        this.element.style.cursor = '';
        this.element.classList.remove('leaflet-interactions-disabled');
        this._interactionDisabled = false;
    }

    isInteractionDisabled() {
        return !!this._interactionDisabled;
    }
}
