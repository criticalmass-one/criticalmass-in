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
        zoom: { type: Number, default: 6 }
    };

    connect() {
        this.initMap();
    }

    initMap() {
        this.map = L.map(this.element, {
            zoomControl: true
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(this.map);

        // View setzen
        if (this.hasCenterLatitudeValue && this.hasCenterLongitudeValue) {
            this.map.setView([this.centerLatitudeValue, this.centerLongitudeValue], this.zoomValue);
        } else {
            // Deutschland-Mitteleinstellung
            this.map.setView([51.1657, 10.4515], this.zoomValue);
        }

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
