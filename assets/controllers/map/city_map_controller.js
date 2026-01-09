import BaseMapController from './base_map_controller';

export default class extends BaseMapController {
    static values = {
        ...BaseMapController.values,
        markerTitle: String
    };

    connect() {
        super.connect();

        this.addCityMarker();
    }

    addCityMarker() {
        if (!this.hasCenterLatitudeValue || !this.hasCenterLongitudeValue) {
            return;
        }

        const marker = this.createMarker(
            this.centerLatitudeValue,
            this.centerLongitudeValue,
            {
                title: this.markerTitleValue || ''
            }
        );

        if (this.hasMarkerTitleValue) {
            marker.bindPopup(this.markerTitleValue);
        }
    }
}
