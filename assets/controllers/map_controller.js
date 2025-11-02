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

        if (polylineLayer) {
            this.fitTo(polylineLayer);
            return;
        }

        if (!hasLegacyCenter && marker) {
            const zoom = this.getLegacyZoom() ?? this.map.getZoom();
            this.map.setView(marker.getLatLng(), zoom);
        }
    }

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

        // 1) manuelle Konfig hat Vorrang
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

        // 2) typisierte Marker (ride, city, location, photo)
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

        // 3) Fallback
        return new L.Icon.Default();
    }

    addPolylineFromDataset() {
        const ds = this.element.dataset;
        const encoded = ds.polyline;
        if (!encoded) return null;

        const color = ds.polylineColor || '#ff0000';

        // bevorzugt: wenn irgendein Plugin L.Polyline.fromEncoded registriert hat
        if (L.Polyline && typeof L.Polyline.fromEncoded === 'function') {
            const pl = L.Polyline.fromEncoded(encoded, { color });
            pl.addTo(this.map);
            return pl;
        }

        // sonst: selbst decoden
        try {
            const latLngs = polylineEncoded.decode(encoded);
            const pl = L.polyline(latLngs, { color, weight: 3 }).addTo(this.map);
            return pl;
        } catch (e) {
            console.warn('map_controller: polyline konnte nicht dekodiert werden', e);
            return null;
        }
    }

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
