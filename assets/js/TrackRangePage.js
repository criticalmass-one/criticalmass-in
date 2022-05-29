import 'bootstrap-slider/dist/bootstrap-slider.min';
import 'bootstrap-slider/dist/css/bootstrap-slider.min.css';
import 'polyline-encoded';

export default class TrackRangePage {
    latLngList;

    constructor(trackRangePage) {
        this.initSlider();
    }

    createInitialPolyline() {
        this.latLngList = JSON.parse(document.getElementById('track_range_latLngList').value);
        const polylineString = L.PolylineUtil.encode(this.latLngList);
        const polyline = L.polyline(polylineString);

        console.log(this.latLngList.length);
        const polylineEvent = new Event('map-polyline-add');
        polylineEvent.identifier = 'range-polyline';
        polylineEvent.polylineString = polylineString;
        polylineEvent.colorString = 'red';
        document.dispatchEvent(polylineEvent);

        return polyline;
    }

    buildSliderOptions() {
        const startPoint = parseInt(document.getElementById('track_range_startPoint').value);
        const endPoint = parseInt(document.getElementById('track_range_endPoint').value);
        const points = document.getElementById('track_range_points').value;

        return {
            id: 'rangeSlider',
            min: 0,
            max: points,
            range: true,
            value: [startPoint, endPoint],
            tooltip: 'hide'
        };
    }

    initSlider() {
        const polyline = this.createInitialPolyline();

        const slider = $('#slider');
        const sliderOptions = this.buildSliderOptions();
        slider.slider(sliderOptions);

        slider.on('slide', (slideEvt) => {
            let endValue = slideEvt.value.pop();
            let beginValue = slideEvt.value.pop();

            document.getElementById('track_range_startPoint').value = beginValue;
            document.getElementById('track_range_endPoint').value = endValue;

            const newLatLngs = this.latLngList.slice();

            endValue -= beginValue;

            newLatLngs.splice(0, beginValue / 10);

            newLatLngs.splice(endValue / 10, (newLatLngs.length - endValue / 10) + 1);

            polyline.setLatLngs(newLatLngs);

            const polylineEvent = new Event('map-polyline-update');
            polylineEvent.identifier = 'range-polyline';
            polylineEvent.polylineString = polyline.encodePath();
            polylineEvent.colorString = 'red';
            document.dispatchEvent(polylineEvent);

            document.getElementById('track_range_reducedPolyline').value = polyline.encodePath();
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const trackRangePage = document.getElementById('track-range-page');

    if (trackRangePage) {
        new TrackRangePage(trackRangePage);
    }
});
