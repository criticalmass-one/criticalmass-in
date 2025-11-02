import BaseMapController from './base_map_controller';
import L from 'leaflet';
import 'leaflet-extra-markers';
import 'leaflet-extra-markers/dist/css/leaflet.extra-markers.min.css';
import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import polylineEncoded from 'polyline-encoded';

export default class extends BaseMapController {
    static values = {
        ...BaseMapController.values,
        citySlug: String,
        rideIdentifier: String,
        locationLatitude: Number,
        locationLongitude: Number
    };

    async connect() {
        super.connect();

        if (!this.hasLocationLatitudeValue || !this.hasLocationLongitudeValue) {
            console.error('[ride-map] locationLatitude/locationLongitude sind Pflicht!');
            return;
        }

        this.map.setView(
            [this.locationLatitudeValue, this.locationLongitudeValue],
            14
        );

        this.addLocationMarker();

        const trackLayer = await this.loadTracks();
        this.loadPhotos();

        if (trackLayer) this.fitTo(trackLayer);
    }

    addLocationMarker() {
        const lat = this.locationLatitudeValue;
        const lng = this.locationLongitudeValue;

        const icon = L.ExtraMarkers.icon({
            icon: 'fa-university',
            markerColor: 'blue',
            shape: 'circle',
            prefix: 'fas'
        });

        const marker = this.createMarker(lat, lng, { icon, title: 'Treffpunkt' });
        marker.bindPopup('Treffpunkt');
    }

    getTrackUrl() {
        if (this.hasCitySlugValue && this.hasRideIdentifierValue) {
            return `/api/${encodeURIComponent(
                this.citySlugValue
            )}/${encodeURIComponent(this.rideIdentifierValue)}/listTracks`;
        }
        return null;
    }

    async loadTracks() {
        const url = this.getTrackUrl();
        if (!url) return null;

        try {
            const trackList = await this.loadJson(url);
            if (!Array.isArray(trackList) || !trackList.length) return null;

            const trackLayer = L.featureGroup();

            for (const track of trackList) {
                const polylineString = track.polylineString || track.polyline;
                if (!polylineString || !track.user) continue;

                const { color_red, color_green, color_blue } = track.user;
                const toHex = (v) =>
                    Math.max(0, Math.min(255, v)).toString(16).padStart(2, '0');
                const color = `#${toHex(color_red)}${toHex(color_green)}${toHex(color_blue)}`;

                const latLngs = polylineEncoded.decode(polylineString);
                L.polyline(latLngs, { color, weight: 3 }).addTo(trackLayer);
            }

            if (trackLayer.getLayers().length) {
                trackLayer.addTo(this.map);
                return trackLayer;
            }

            return null;
        } catch (err) {
            console.warn('Ride tracks load failed', err);
            return null;
        }
    }

    getPhotosUrl() {
        if (this.hasCitySlugValue && this.hasRideIdentifierValue) {
            return `/api/${encodeURIComponent(
                this.citySlugValue
            )}/${encodeURIComponent(this.rideIdentifierValue)}/listPhotos`;
        }
        return null;
    }

    async loadPhotos() {
        const url = this.getPhotosUrl();
        if (!url) return;

        try {
            const photoList = await this.loadJson(url);
            if (!Array.isArray(photoList) || !photoList.length) return;

            const photoLayer = L.markerClusterGroup({
                showCoverageOnHover: false,
                iconCreateFunction: () => this.getPhotoIcon()
            });

            for (const photo of photoList) {
                const lat = parseFloat(photo.latitude);
                const lng = parseFloat(photo.longitude);
                if (!Number.isFinite(lat) || !Number.isFinite(lng)) continue;

                const marker = L.marker([lat, lng], {
                    icon: this.getPhotoIcon(),
                    title: photo.title || 'Foto'
                });

                if (photo.thumbnail || photo.url) {
                    const src = photo.thumbnail || photo.url;
                    marker.bindPopup(
                        `<img src='${src}' alt='' style='max-width:150px;'>`
                    );
                }

                photoLayer.addLayer(marker);
            }

            photoLayer.addTo(this.map);
        } catch (err) {
            console.warn('Ride photos load failed', err);
        }
    }

    getPhotoIcon() {
        return L.ExtraMarkers.icon({
            icon: 'fa-camera',
            markerColor: 'yellow',
            shape: 'square',
            prefix: 'fas'
        });
    }
}
