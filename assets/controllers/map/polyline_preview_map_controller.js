import BaseMapController from './base_map_controller';
import L from 'leaflet';
import polylineEncoded from 'polyline-encoded';

/**
 * Renders a small, non-interactive preview of an encoded polyline — used in the
 * bulk-upload review list to show each candidate track.
 */
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
        polyline: String,
    };

    connect() {
        super.connect();

        this.disableInteraction();

        if (!this.hasPolylineValue || !this.polylineValue) {
            return;
        }

        const latLngs = polylineEncoded.decode(this.polylineValue);

        if (!latLngs.length) {
            return;
        }

        const line = L.polyline(latLngs, { color: '#d63384', weight: 3 }).addTo(this.map);

        this.fitTo(line);
    }
}
