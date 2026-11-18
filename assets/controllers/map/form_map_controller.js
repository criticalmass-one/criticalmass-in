import BaseMapController from './base_map_controller';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Erst holen, wenn wirklich eine Karte im Dokument steht. Ohne diese Zeile
// landet die gesamte Kartenmaschine im Startbuendel und wird auf JEDER Seite
// geladen — auch im Impressum und in der Datenschutzerklaerung, wo keine
// Karte steht. Es geht dabei nicht um ein paar Kilobyte: base_map_controller
// zieht MapLibre GL samt Leaflet herein, zusammen ueber fuenf Megabyte, die
// der Browser entpacken und auswerten muss, bevor irgendetwas bedienbar ist.
//
// Die Schreibweise ist vorgegeben: Der lazy-controller-loader erkennt genau
// diesen einzeiligen Kommentar und lehnt jede andere Form ab.
/* stimulusFetch: 'lazy' */
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

        // geocoding_controller meldet seinen Treffer am document, nicht an dieser Karte
        this.onGeocodingResult = this.onGeocodingResult.bind(this);
        document.addEventListener('geocoding-result', this.onGeocodingResult);
    }

    disconnect() {
        document.removeEventListener('geocoding-result', this.onGeocodingResult);
        this.marker = null;
        super.disconnect();
    }

    onGeocodingResult(event) {
        const lat = parseFloat(event.detail?.lat);
        const lng = parseFloat(event.detail?.lon);
        if (!this.map || Number.isNaN(lat) || Number.isNaN(lng)) return;

        if (this.marker) {
            this.marker.setLatLng([lat, lng]);
            this.updateInputs(lat, lng);
        }

        // Nominatim liefert boundingbox als [Süd, Nord, West, Ost]
        const bbox = (event.detail.boundingbox || []).map(parseFloat);
        if (bbox.length === 4 && !bbox.some(Number.isNaN)) {
            this.map.fitBounds([[bbox[0], bbox[2]], [bbox[1], bbox[3]]], { maxZoom: 16 });
        } else {
            this.map.setView([lat, lng], Math.max(this.map.getZoom(), 15));
        }
    }

    updateInputs(lat, lng) {
        if (!this.latInput || !this.lngInput) return;

        this.latInput.value = lat.toFixed(6);
        this.lngInput.value = lng.toFixed(6);
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

        this.latInput = latInput;
        this.lngInput = lngInput;

        this.marker = L.marker([startLat, startLng], {
            draggable: true,
            autoPan: true,
            icon: this.buildIcon()
        }).addTo(this.map);

        this.updateInputs(startLat, startLng);

        this.marker.on('moveend', (e) => {
            const ll = e.target.getLatLng();
            this.updateInputs(ll.lat, ll.lng);
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
