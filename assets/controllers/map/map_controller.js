// controllers/map_controller.js
import BaseMapController from './base_map_controller';
import L from 'leaflet';
import polylineEncoded from 'polyline-encoded';

export default class extends BaseMapController {
    static values = {
        // aus BaseMap erneut deklarieren (Stimulus vererbt static values nicht!)
        centerLatitude: Number,
        centerLongitude: Number,
        zoom: { type: Number, default: 12 },
        maptilerKey: String,

        // Marker + Lock
        markerLatitude: Number,
        markerLongitude: Number,
        markerType: String,
        markerIcon: String,
        markerColor: String,
        markerShape: String,
        markerPrefix: String,
        lockMap: Boolean,

        // Polyline
        polyline: String,
        polylineColor: String
    };

    async connect() {
        super.connect();

        const marker = this.addMarkerFromValues();
        const polylineLayer = this.addPolylineFromValues();

        this.lockIfRequested();

        if (polylineLayer) {
            this.fitTo(polylineLayer);
            return;
        }

        // Wenn kein Polyline vorhanden ist: optional auf Marker zentrieren,
        // aber nur wenn KEIN explizites Center gesetzt wurde.
        if (marker && !(this.hasCenterLatitudeValue && this.hasCenterLongitudeValue)) {
            this.map.setView(marker.getLatLng(), this.zoomValue ?? this.map.getZoom());
        }
    }

    addMarkerFromValues() {
        if (!this.hasMarkerLatitudeValue || !this.hasMarkerLongitudeValue) return null;
        const lat = this.markerLatitudeValue;
        const lng = this.markerLongitudeValue;
        const icon = this.buildIconFromValues();
        return this.createMarker(lat, lng, { icon });
    }

    buildIconFromValues() {
        const hasManual =
            this.hasMarkerIconValue ||
            this.hasMarkerColorValue ||
            this.hasMarkerShapeValue ||
            this.hasMarkerPrefixValue;

        if (hasManual && L.ExtraMarkers && typeof L.ExtraMarkers.icon === 'function') {
            return L.ExtraMarkers.icon({
                icon: this.markerIconValue || 'fa-map-marker-alt',
                markerColor: this.markerColorValue || 'blue',
                shape: this.markerShapeValue || 'circle',
                prefix: this.markerPrefixValue || 'fas'
            });
        }

        if (this.hasMarkerTypeValue && L.ExtraMarkers && typeof L.ExtraMarkers.icon === 'function') {
            const type = this.mapMarkerTypeValue;
            if (type === 'ride')     return L.ExtraMarkers.icon({ icon: 'fa-bicycle',       markerColor: 'red',    shape: 'circle', prefix: 'fas' });
            if (type === 'city')     return L.ExtraMarkers.icon({ icon: 'fa-university',     markerColor: 'blue',   shape: 'circle', prefix: 'fas' });
            if (type === 'photo')    return L.ExtraMarkers.icon({ icon: 'fa-camera',         markerColor: 'yellow', shape: 'square', prefix: 'fas' });
            if (type === 'location') return L.ExtraMarkers.icon({ icon: 'fa-map-marker-alt', markerColor: 'green',  shape: 'circle', prefix: 'fas' });
        }

        return new L.Icon.Default();
    }

    addPolylineFromValues() {
        if (!this.hasPolylineValue) return null;
        const encoded = this.polylineValue;
        const color = this.normalizeColor(this.polylineColorValue) || '#ff0000';

        if (L.Polyline && typeof L.Polyline.fromEncoded === 'function') {
            const pl = L.Polyline.fromEncoded(encoded, { color });
            pl.addTo(this.map);
            return pl;
        }
        try {
            const latLngs = polylineEncoded.decode(encoded);
            return L.polyline(latLngs, { color, weight: 3 }).addTo(this.map);
        } catch (e) {
            console.warn('map_controller: polyline konnte nicht dekodiert werden', e);
            return null;
        }
    }

    lockIfRequested() {
        if (this.hasLockMapValue && this.lockMapValue === true) {
            this.disableInteraction();
            return;
        }
        // Fallback für alte Attribute weiter möglich:
        const ds = this.element.dataset;
        if (ds.lockMap === 'true' || ds.lockMap === '1') {
            this.disableInteraction();
        }
    }
}
