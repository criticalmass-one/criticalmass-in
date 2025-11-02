// assets/controllers/ride_map_controller.js
import BaseMapController from './base_map_controller';
import L from 'leaflet';
import 'leaflet-extra-markers/dist/css/leaflet.extra-markers.min.css';
import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import polylineEncoded from 'polyline-encoded';

export default class extends BaseMapController {
    static values = {
        ...BaseMapController.values,

        // aus dem Template
        citySlug: String,
        rideIdentifier: String,

        // alternative, explizit gesetzte URLs
        trackUrl: String,
        photosUrl: String,

        // Treffpunkt
        meetingLatitude: Number,
        meetingLongitude: Number
    };

    connect() {
        // Basiskarte (MapTiler, Center, Fallback …)
        super.connect();

        this.addMeetingMarker();
        this.loadTracks();
        this.loadPhotos();
    }

    /* --------------------------------------------------------
     *  Marker für Treffpunkt
     * ----------------------------------------------------- */
    addMeetingMarker() {
        // 1. Priorität: expliziter Treffpunkt
        if (this.hasMeetingLatitudeValue && this.hasMeetingLongitudeValue) {
            this.createRideMarker(this.meetingLatitudeValue, this.meetingLongitudeValue);
            return;
        }

        // 2. Fallback: Mittelpunkt der Karte
        if (this.hasCenterLatitudeValue && this.hasCenterLongitudeValue) {
            this.createRideMarker(this.centerLatitudeValue, this.centerLongitudeValue);
        }
    }

    createRideMarker(lat, lng) {
        // blauer ExtraMarkers-Marker mit fa-university
        let icon;

        if (L.ExtraMarkers && typeof L.ExtraMarkers.icon === 'function') {
            icon = L.ExtraMarkers.icon({
                icon: 'fa-university',
                markerColor: 'blue',
                shape: 'circle',
                prefix: 'fas'
            });
        } else {
            // Fallback, falls ExtraMarkers mal nicht geladen
            icon = new L.Icon.Default();
        }

        const marker = this.createMarker(lat, lng, {
            icon,
            title: 'Treffpunkt'
        });

        marker.bindPopup('Treffpunkt');

        return marker;
    }

    /* --------------------------------------------------------
     *  Tracks laden – alte Logik (/listTracks) zuerst
     * ----------------------------------------------------- */
    getTrackUrl() {
        // 1) explizit gesetzt?
        if (this.hasTrackUrlValue) {
            return this.trackUrlValue;
        }

        // 2) aus citySlug + rideIdentifier bauen (altes Schema)
        if (this.hasCitySlugValue && this.hasRideIdentifierValue) {
            // wie früher:
            // /api/{citySlug}/{rideIdentifier}/listTracks
            return `/api/${encodeURIComponent(this.citySlugValue)}/${encodeURIComponent(this.rideIdentifierValue)}/listTracks`;
        }

        return null;
    }

    async loadTracks() {
        const url = this.getTrackUrl();
        if (!url) return;

        try {
            const trackList = await this.loadJson(url);

            // falls API mal {} und nicht [] liefert
            if (!Array.isArray(trackList) || !trackList.length) {
                return;
            }

            const trackLayer = L.featureGroup();

            for (const track of trackList) {
                const polylineString = track.polylineString || track.polyline || null;
                if (!polylineString) continue;

                // wie früher: encoded polyline -> Leaflet-Poyline
                const latLngs = polylineEncoded.decode(polylineString);
                const polyline = L.polyline(latLngs, {
                    color: 'red',
                    weight: 3
                });
                polyline.addTo(trackLayer);
            }

            // auf die Tracks zoomen
            if (trackLayer.getLayers().length) {
                trackLayer.addTo(this.map);
                this.fitTo(trackLayer);
            }
        } catch (err) {
            console.warn('Ride tracks load failed', err);
        }
    }

    /* --------------------------------------------------------
     *  Fotos laden – alte Logik (/listPhotos) + MarkerCluster
     * ----------------------------------------------------- */
    getPhotosUrl() {
        // 1) explizit gesetzt?
        if (this.hasPhotosUrlValue) {
            return this.photosUrlValue;
        }

        // 2) aus citySlug + rideIdentifier bauen (altes Schema)
        if (this.hasCitySlugValue && this.hasRideIdentifierValue) {
            // wie früher:
            // /api/{citySlug}/{rideIdentifier}/listPhotos
            return `/api/${encodeURIComponent(this.citySlugValue)}/${encodeURIComponent(this.rideIdentifierValue)}/listPhotos`;
        }

        return null;
    }

    async loadPhotos() {
        const url = this.getPhotosUrl();
        if (!url) return;

        try {
            const photoList = await this.loadJson(url);
            if (!Array.isArray(photoList) || !photoList.length) {
                return;
            }

            // Cluster-Layer wie früher
            const that = this;
            const photoLayer = L.markerClusterGroup({
                showCoverageOnHover: false,
                iconCreateFunction: function () {
                    // Cluster bekommt einfach dasselbe Icon wie ein Foto
                    return that.getPhotoIcon();
                }
            });

            for (const photo of photoList) {
                if (!photo.latitude || !photo.longitude) continue;

                const lat = parseFloat(photo.latitude);
                const lng = parseFloat(photo.longitude);
                if (!Number.isFinite(lat) || !Number.isFinite(lng)) continue;

                const marker = L.marker([lat, lng], {
                    icon: that.getPhotoIcon(),
                    title: photo.title || 'Foto'
                });

                if (photo.thumbnail || photo.url) {
                    const src = photo.thumbnail || photo.url;
                    marker.bindPopup(`<img src='${src}' alt='' style='max-width:150px;'>`);
                }

                photoLayer.addLayer(marker);
            }

            photoLayer.addTo(this.map);
        } catch (err) {
            console.warn('Ride photos load failed', err);
        }
    }

    /* --------------------------------------------------------
     *  Foto-Icon (ExtraMarkers, gelb)
     * ----------------------------------------------------- */
    getPhotoIcon() {
        if (L.ExtraMarkers && typeof L.ExtraMarkers.icon === 'function') {
            return L.ExtraMarkers.icon({
                icon: 'fa-camera',
                markerColor: 'yellow',
                shape: 'square',
                prefix: 'fas'
            });
        }
        return new L.Icon.Default();
    }
}
