import BaseMapController from './base_map_controller';
import L from 'leaflet';
import polylineEncoded from 'polyline-encoded';

import 'bootstrap-slider/dist/bootstrap-slider.min';
import 'bootstrap-slider/dist/css/bootstrap-slider.min.css';

export default class extends BaseMapController {
    connect() {
        super.connect();

        this.initTrackRange();
    }

    initTrackRange() {
        const latLngInput = document.getElementById('track_range_latLngList');

        if (!latLngInput) {
            console.warn('[track-range] #track_range_latLngList nicht gefunden');

            return;
        }

        this.latLngList = JSON.parse(latLngInput.value);
        this.currentLatLngs = this.latLngList.slice();

        this.trackLayer = L.polyline(this.latLngList, {
            color: 'red',
            weight: 4
        }).addTo(this.map);

        this.fitTo(this.trackLayer);

        this.dispatchPolylineAdd(this.encodeLatLngs(this.latLngList));

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
            let endValue = evt.value.pop();
            let beginValue = evt.value.pop();

            if (startInput) {
                startInput.value = beginValue;
            }

            if (endInput) {
                endInput.value = endValue;
            }

            const newLatLngs = this.latLngList.slice();

            let croppedEnd = endValue - beginValue;

            newLatLngs.splice(0, beginValue / 10);

            newLatLngs.splice(croppedEnd / 10, (newLatLngs.length - croppedEnd / 10) + 1);

            this.trackLayer.setLatLngs(newLatLngs);

            const encoded = this.encodeLatLngs(newLatLngs);

            if (reducedInput) {
                reducedInput.value = encoded;
            }

            this.dispatchPolylineUpdate(encoded);
        });
    }

    encodeLatLngs(latLngs) {
        if (L.PolylineUtil && typeof L.PolylineUtil.encode === 'function') {
            return L.PolylineUtil.encode(latLngs);
        }

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
