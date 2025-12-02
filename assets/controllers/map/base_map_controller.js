import { Controller } from '@hotwired/stimulus';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

import maplibregl from 'maplibre-gl';
import 'maplibre-gl/dist/maplibre-gl.css';
import '@maplibre/maplibre-gl-leaflet';

import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
import iconUrl from 'leaflet/dist/images/marker-icon.png';
import shadowUrl from 'leaflet/dist/images/marker-shadow.png';

L.Icon.Default.mergeOptions({ iconRetinaUrl, iconUrl, shadowUrl });

export default class extends Controller {
    static values = {
        centerLatitude: Number,
        centerLongitude: Number,
        zoom: { type: Number, default: 12 },
        lockMap: String,
    };

    connect() {
        this.initMap();
    }

    initMap() {
        const locked = this.hasLockMapValue && this.lockMapValue === 'true';

        this.map = L.map(this.element, {
            zoomControl: !locked,
            maxBounds: [[180, -Infinity], [-180, Infinity]],
            maxBoundsViscosity: 1,
            minZoom: 1,
        });

        const lat = this.hasCenterLatitudeValue ? this.centerLatitudeValue : 51.1657;
        const lng = this.hasCenterLongitudeValue ? this.centerLongitudeValue : 10.4515;
        const zoom = this.zoomValue ?? 12;
        this.map.setView([lat, lng], zoom);

        L.maplibreGL({
            style: 'https://tiles.openfreemap.org/styles/liberty',
        }).addTo(this.map);

        setTimeout(() => this.map.invalidateSize(), 80);
    }

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

        if (/^rgb\s*\(/i.test(c)) {
            const m = c.match(/rgb\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/i);

            if (!m) {
                return c;
            }

            const toHex = (v) => {
                const n = Math.max(0, Math.min(255, parseInt(v, 10)));
                return n.toString(16).padStart(2, '0');
            };

            return `#${toHex(m[1])}${toHex(m[2])}${toHex(m[3])}`;
        }

        if (c.startsWith('#')) {
            return c;
        }

        if (/^[0-9a-fA-F]{6}$/.test(c)) {
            return `#${c}`;
        }

        if (/^[0-9a-fA-F]{3}$/.test(c)) {
            return `#${c}`;
        }

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

        if (this.map.zoomControl) {
            this.map.removeControl(this.map.zoomControl);
            this.map.zoomControl = null;
        }

        if (this.map.attributionControl) {
            this.map.attributionControl.remove();
            this.map.attributionControl = null;
        }

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
