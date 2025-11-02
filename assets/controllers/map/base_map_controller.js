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
}
