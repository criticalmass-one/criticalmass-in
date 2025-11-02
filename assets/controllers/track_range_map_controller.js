// controllers/track_range_map_controller.js
import BaseMapController from './base_map_controller';
import L from 'leaflet';
import polylineEncoded from 'polyline-encoded';

// optional – falls du bootstrap-slider über webpack einbindest:
import 'bootstrap-slider/dist/bootstrap-slider.min';
import 'bootstrap-slider/dist/css/bootstrap-slider.min.css';

export default class extends BaseMapController {
    connect() {
        // Karte bauen
        super.connect();

        // Track + Slider initialisieren
        this.initTrackRange();
    }

    initTrackRange() {
        // 1. LatLng-Liste aus dem Hidden-Field
        const latLngInput = document.getElementById('track_range_latLngList');
        if (!latLngInput) {
            console.warn('[track-range] #track_range_latLngList nicht gefunden');
            return;
        }

        // das ist bei dir ein JSON-Array: [[lat,lng],[lat,lng],...]
        this.latLngList = JSON.parse(latLngInput.value);
        this.currentLatLngs = this.latLngList.slice();

        // 2. Polyline auf die Karte bringen
        this.trackLayer = L.polyline(this.latLngList, {
            color: 'red',
            weight: 4
        }).addTo(this.map);

        this.fitTo(this.trackLayer);

        // 3. initiales Event wie früher (falls noch irgendwo gehört wird)
        this.dispatchPolylineAdd(this.encodeLatLngs(this.latLngList));

        // 4. Slider initialisieren
        this.initSlider();
    }

    initSlider() {
        const sliderEl = document.getElementById('slider');
        if (!sliderEl) {
            console.warn('[track-range] #slider nicht gefunden');
            return;
        }

        const startInput = document.getElementById('track_range_startPoint');
        const endInput = document.getElementById('track_range_endPoint');
        const pointsInput = document.getElementById('track_range_points');
        const reducedInput = document.getElementById('track_range_reducedPolyline');

        if (!pointsInput) {
            console.warn('[track-range] #track_range_points nicht gefunden');
            return;
        }

        const startPoint = parseInt(startInput?.value ?? '0', 10);
        const endPoint = parseInt(endInput?.value ?? '0', 10);
        const points = parseInt(pointsInput.value, 10);

        // wir gehen davon aus, dass du bootstrap-slider + jQuery geladen hast
        const $ = window.$ || window.jQuery;
        if (!$ || !$.fn.slider) {
            console.warn('[track-range] bootstrap-slider nicht verfügbar');
            return;
        }

        const $slider = $(sliderEl);
        $slider.slider({
            id: 'rangeSlider',
            min: 0,
            max: points,
            range: true,
            value: [startPoint, endPoint],
            tooltip: 'hide'
        });

        $slider.on('slide', (evt) => {
            // bootstrap-slider liefert ein Array
            // aber manchmal kommt es „rückwärts“, daher wie im alten Code:
            let endValue = evt.value.pop();
            let beginValue = evt.value.pop();

            // Hidden-Felder aktualisieren
            if (startInput) startInput.value = beginValue;
            if (endInput) endInput.value = endValue;

            // alte Logik: du hast die Sliderwerte /10 auf die Punkte gemappt
            // (weil du offenbar 10er-Schritte im Slider nutzt)
            const newLatLngs = this.latLngList.slice();

            // relative Länge ermitteln
            let croppedEnd = endValue - beginValue;

            // vorne abschneiden
            newLatLngs.splice(0, beginValue / 10);

            // hinten abschneiden
            newLatLngs.splice(croppedEnd / 10, (newLatLngs.length - croppedEnd / 10) + 1);

            // Karte aktualisieren
            this.trackLayer.setLatLngs(newLatLngs);

            // neue encoded Polyline berechnen
            const encoded = this.encodeLatLngs(newLatLngs);

            // Hidden-Feld für Controller/Symfony setzen
            if (reducedInput) {
                reducedInput.value = encoded;
            }

            // Event wie früher feuern
            this.dispatchPolylineUpdate(encoded);
        });
    }

    encodeLatLngs(latLngs) {
        // 1. Leaflet hat evtl. ein Plugin
        if (L.PolylineUtil && typeof L.PolylineUtil.encode === 'function') {
            return L.PolylineUtil.encode(latLngs);
        }
        // 2. sonst unser importiertes polyline-encoded
        return polylineEncoded.encode(latLngs);
    }

    dispatchPolylineAdd(encoded) {
        const evt = new Event('map-polyline-add');
        evt.identifier = 'range-polyline';
        evt.polylineString = encoded;
        evt.colorString = 'red';
        document.dispatchEvent(evt);
    }

    dispatchPolylineUpdate(encoded) {
        const evt = new Event('map-polyline-update');
        evt.identifier = 'range-polyline';
        evt.polylineString = encoded;
        evt.colorString = 'red';
        document.dispatchEvent(evt);
    }
}
