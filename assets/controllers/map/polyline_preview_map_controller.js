import BaseMapController from './base_map_controller';
import L from 'leaflet';
import polylineEncoded from 'polyline-encoded';

/**
 * Renders a small, non-interactive preview of an encoded polyline — used in the
 * bulk-upload review list to show each candidate track.
 */
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
