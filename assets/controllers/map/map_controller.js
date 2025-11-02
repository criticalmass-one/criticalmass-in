// controllers/map_controller.js
import BaseMapController from './base_map_controller';
import L from 'leaflet';
import polylineEncoded from 'polyline-encoded';

export default class extends BaseMapController {
    async connect() {
        super.connect();

        const hasLegacyCenter = this.applyLegacyCenter();
        const marker = this.addMarkerFromDataset();
        const polylineLayer = this.addPolylineFromDataset();

        this.lockIfRequested();

        // 1) wenn Polyline da → immer auf Polyline zoomen
        if (polylineLayer) {
            this.fitTo(polylineLayer);
            return;
        }

        // 2) sonst: wenn kein eigenes Center, aber Marker → auf Marker
        if (!hasLegacyCenter && marker) {
            const zoom = this.getLegacyZoom() ?? this.map.getZoom();
            this.map.setView(marker.getLatLng(), zoom);
        }
    }

    // alte Attribute für Center unterstützen
    applyLegacyCenter() {
        const ds = this.element.dataset;
        const lat = parseFloat(ds.mapCenterLatitude);
        const lng = parseFloat(ds.mapCenterLongitude);
        const zoom = this.getLegacyZoom();

        if (Number.isFinite(lat) && Number.isFinite(lng)) {
            this.map.setView([lat, lng], zoom ?? this.map.getZoom());
            return true;
        }
        return false;
    }

    getLegacyZoom() {
        const z = this.element.dataset.mapZoomlevel;
        if (z == null) return null;
        const zi = parseInt(z, 10);
        return Number.isFinite(zi) ? zi : null;
    }

    // Marker aus data-map-marker-* lesen
    addMarkerFromDataset() {
        const ds = this.element.dataset;

        const lat = parseFloat(ds.mapMarkerLatitude);
        const lng = parseFloat(ds.mapMarkerLongitude);

        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
            return null;
        }

        const icon = this.buildIconFromDataset(ds);
        return this.createMarker(lat, lng, { icon });
    }

    buildIconFromDataset(ds) {
        const iconName = ds.mapMarkerIcon;
        const markerColor = ds.mapMarkerColor;
        const markerShape = ds.mapMarkerShape;
        const markerPrefix = ds.mapMarkerPrefix;
        const type = ds.mapMarkerType;

        // manuelle Konfiguration hat Vorrang
        if (iconName || markerColor || markerShape || markerPrefix) {
            if (L.ExtraMarkers && typeof L.ExtraMarkers.icon === 'function') {
                return L.ExtraMarkers.icon({
                    icon: iconName || 'fa-map-marker-alt',
                    markerColor: markerColor || 'blue',
                    shape: markerShape || 'circle',
                    prefix: markerPrefix || 'fas'
                });
            }
            return new L.Icon.Default();
        }

        // typisierte Marker
        if (type && L.ExtraMarkers && typeof L.ExtraMarkers.icon === 'function') {
            if (type === 'ride') {
                return L.ExtraMarkers.icon({
                    icon: 'fa-bicycle',
                    markerColor: 'red',
                    shape: 'circle',
                    prefix: 'fas'
                });
            }
            if (type === 'city') {
                return L.ExtraMarkers.icon({
                    icon: 'fa-university',
                    markerColor: 'blue',
                    shape: 'circle',
                    prefix: 'fas'
                });
            }
            if (type === 'photo') {
                return L.ExtraMarkers.icon({
                    icon: 'fa-camera',
                    markerColor: 'yellow',
                    shape: 'square',
                    prefix: 'fas'
                });
            }
            if (type === 'location') {
                return L.ExtraMarkers.icon({
                    icon: 'fa-map-marker-alt',
                    markerColor: 'green',
                    shape: 'circle',
                    prefix: 'fas'
                });
            }
        }

        // Fallback
        return new L.Icon.Default();
    }

    // Polyline aus data-polyline laden
    addPolylineFromDataset() {
        const ds = this.element.dataset;
        const encoded = ds.polyline;
        if (!encoded) return null;

        const color = this.normalizeColor(ds.polylineColor) || '#ff0000';

        // wenn Leaflet-Encoded-Plugin da ist
        if (L.Polyline && typeof L.Polyline.fromEncoded === 'function') {
            const pl = L.Polyline.fromEncoded(encoded, { color });
            pl.addTo(this.map);
            return pl;
        }

        // sonst selbst decoden
        try {
            const latLngs = polylineEncoded.decode(encoded);
            const pl = L.polyline(latLngs, { color, weight: 3 }).addTo(this.map);
            return pl;
        } catch (e) {
            console.warn('map_controller: polyline konnte nicht dekodiert werden', e);
            return null;
        }
    }

    // Farbangaben robuster machen
    normalizeColor(input) {
        if (!input) return null;
        const c = input.trim();

        // rgb(…, …, …)
        if (c.startsWith('rgb')) {
            const m = c.match(/rgb\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/i);
            if (!m) return c;
            const toHex = (v) => {
                const n = Math.max(0, Math.min(255, parseInt(v, 10)));
                return n.toString(16).padStart(2, '0');
            };
            return `#${toHex(m[1])}${toHex(m[2])}${toHex(m[3])}`;
        }

        // schon ein hex mit #
        if (c.startsWith('#')) return c;

        // nackter hex: 6 oder 3 Stellen
        if (/^[0-9a-fA-F]{6}$/.test(c)) return `#${c}`;
        if (/^[0-9a-fA-F]{3}$/.test(c)) return `#${c}`;

        // alles andere (color names) durchreichen
        return c;
    }

    // ggf. Karte sperren
    lockIfRequested() {
        const ds = this.element.dataset;
        if (ds.lockMap === 'true' || ds.lockMap === '1') {
            this.disableInteraction();
        }
    }

    disableInteraction() {
        if (!this.map) return;
        this.map.dragging.disable();
        this.map.touchZoom.disable();
        this.map.doubleClickZoom.disable();
        this.map.scrollWheelZoom.disable();
        this.map.boxZoom.disable();
        this.map.keyboard.disable();

        const zoomControl = this.element.querySelector('.leaflet-control-zoom');
        if (zoomControl) zoomControl.remove();

        this.element.style.cursor = 'default';
    }
}
