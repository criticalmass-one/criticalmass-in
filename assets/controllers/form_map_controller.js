// assets/controllers/form_map_controller.js
import BaseMapController from './base_map_controller';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

export default class extends BaseMapController {
    static values = {
        ...BaseMapController.values,
        markerLatitudeTarget: String,
        markerLongitudeTarget: String,
        markerIcon: { type: String, default: 'fa-bicycle' },
        markerColor: { type: String, default: 'red' },
        markerShape: { type: String, default: 'circle' },
        markerPrefix: { type: String, default: 'fas' }
    };

    connect() {
        super.connect();
        this.addDraggableMarker();
    }

    addDraggableMarker() {
        if (!this.hasMarkerLatitudeTargetValue || !this.hasMarkerLongitudeTargetValue) return;

        const latInput = document.getElementById(this.markerLatitudeTargetValue);
        const lngInput = document.getElementById(this.markerLongitudeTargetValue);
        if (!latInput || !lngInput) return;

        const startLat =
            parseFloat(latInput.value) ||
            (this.hasCenterLatitudeValue ? this.centerLatitudeValue : 51.1657);
        const startLng =
            parseFloat(lngInput.value) ||
            (this.hasCenterLongitudeValue ? this.centerLongitudeValue : 10.4515);

        const marker = L.marker([startLat, startLng], {
            draggable: true,
            autoPan: true,
            icon: this.buildIcon()
        }).addTo(this.map);

        latInput.value = startLat.toFixed(6);
        lngInput.value = startLng.toFixed(6);

        marker.on('moveend', (e) => {
            const ll = e.target.getLatLng();
            latInput.value = ll.lat.toFixed(6);
            lngInput.value = ll.lng.toFixed(6);
        });
    }

    buildIcon() {
        if (L.ExtraMarkers && typeof L.ExtraMarkers.icon === 'function') {
            return L.ExtraMarkers.icon({
                icon: this.markerIconValue,
                markerColor: this.markerColorValue,
                shape: this.markerShapeValue,
                prefix: this.markerPrefixValue
            });
        }
        return new L.Icon.Default();
    }
}
