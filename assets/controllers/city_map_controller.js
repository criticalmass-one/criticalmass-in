// controllers/city_map_controller.js
import BaseMapController from './base_map_controller';
import L from 'leaflet';

export default class extends BaseMapController {
    static values = {
        ...BaseMapController.values,
        apiUrl: String,           // z. B. /api/cities/lueneburg
        markerTitle: String       // für Popup
    };

    connect() {
        // zuerst Karte erstellen
        super.connect();

        // danach Stadt-Marker setzen
        this.addCityMarker();

        // und danach (optional) Daten laden
        if (this.hasApiUrlValue) {
            this.loadCityData();
        }
    }

    addCityMarker() {
        if (!this.hasCenterLatitudeValue || !this.hasCenterLongitudeValue) {
            return;
        }

        const marker = this.createMarker(
            this.centerLatitudeValue,
            this.centerLongitudeValue,
            {
                // hier nur Default-Icon, Basisklasse bleibt schlank
                title: this.markerTitleValue || ''
            }
        );

        if (this.hasMarkerTitleValue) {
            marker.bindPopup(this.markerTitleValue);
        }
    }

    async loadCityData() {
        try {
            const data = await this.loadJson(this.apiUrlValue);
            // hier kannst du z. B. noch weitere Marker ausgeben
            // oder die Karte anders zentrieren
            // console.log('city data', data);
        } catch (e) {
            // still sein ist okay, du kannst auch console.warn schreiben
            console.warn('City API failed', e);
        }
    }
}
