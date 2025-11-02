// controllers/ride_map_controller.js
import BaseMapController from './base_map_controller';
import L from 'leaflet';
// wenn deine Tracks encoded sind, nimm das hier dazu
// import polylineEncoded from 'polyline-encoded';

export default class extends BaseMapController {
    static values = {
        ...BaseMapController.values,

        // zum automatischen Zusammenbauen
        citySlug: String,
        rideIdentifier: String,

        // oder direkt
        trackUrl: String,
        photosUrl: String,

        // Treffpunkt
        meetingLatitude: Number,
        meetingLongitude: Number
    };

    connect() {
        super.connect();

        this.addMeetingMarker();
        this.loadTrack();
        this.loadPhotos();
    }

    addMeetingMarker() {
        // 1. Priorität: expliziter Treffpunkt
        if (this.hasMeetingLatitudeValue && this.hasMeetingLongitudeValue) {
            this.createRideMarker(this.meetingLatitudeValue, this.meetingLongitudeValue);
            return;
        }

        // 2. Fallback: Kartenmittelpunkt
        if (this.hasCenterLatitudeValue && this.hasCenterLongitudeValue) {
            this.createRideMarker(this.centerLatitudeValue, this.centerLongitudeValue);
        }
    }

    createRideMarker(lat, lng) {
        // hier kannst du dein Extra-Marker-Setup reinnehmen, wenn du willst
        const marker = this.createMarker(lat, lng, {
            title: 'Treffpunkt'
        });
        marker.bindPopup('Treffpunkt');
        return marker;
    }

    getTrackUrl() {
        if (this.hasTrackUrlValue) {
            return this.trackUrlValue;
        }
        if (this.hasCitySlugValue && this.hasRideIdentifierValue) {
            return `/api/${this.citySlugValue}/${this.rideIdentifierValue}/track?format=geojson`;
        }
        return null;
    }

    async loadTrack() {
        const url = this.getTrackUrl();
        if (!url) return;

        try {
            const geojson = await this.loadJson(url);

            const layer = L.geoJSON(geojson, {
                style: {
                    color: '#ff0000',
                    weight: 3
                }
            }).addTo(this.map);

            this.fitTo(layer);
        } catch (e) {
            console.warn('Ride track load failed', e);
        }
    }

    getPhotosUrl() {
        if (this.hasPhotosUrlValue) {
            return this.photosUrlValue;
        }
        if (this.hasCitySlugValue && this.hasRideIdentifierValue) {
            return `/api/${this.citySlugValue}/${this.rideIdentifierValue}/photos?format=json`;
        }
        return null;
    }

    async loadPhotos() {
        const url = this.getPhotosUrl();
        if (!url) return;

        try {
            const list = await this.loadJson(url);
            if (!Array.isArray(list) || !list.length) return;

            const group = this.createFeatureGroup();

            for (const photo of list) {
                const lat = parseFloat(photo.latitude);
                const lng = parseFloat(photo.longitude);
                if (!Number.isFinite(lat) || !Number.isFinite(lng)) continue;

                const marker = L.marker([lat, lng], {
                    // hier könntest du auch ein Kamera-Icon nehmen
                    title: photo.title || 'Foto'
                });

                if (photo.thumbnail || photo.url) {
                    const src = photo.thumbnail || photo.url;
                    marker.bindPopup(`<img src='${src}' alt='' style='max-width:150px;'>`);
                }

                group.addLayer(marker);
            }

            // nicht zwingend fitBounds, weil Track das schon macht
            // this.fitTo(group);
        } catch (e) {
            console.warn('Ride photos load failed', e);
        }
    }
}
