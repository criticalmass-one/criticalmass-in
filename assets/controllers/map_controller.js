import { Controller } from '@hotwired/stimulus';
import L from 'leaflet';
import polylineEncoded from 'polyline-encoded';
import markerCluster from 'leaflet.markercluster';
import extraMarkers from 'leaflet-extra-markers';

const TYPE_RIDE = 'ride';
const TYPE_CITY = 'city';
const TYPE_PHOTO = 'photo';
const TYPE_SUBRIDE = 'subride';
const TYPE_LOCATION = 'location';

export default class extends Controller {
    static targets = [
        'markerLatitude',
        'markerLongitude'
    ];

    connect() {
        this.polylineList = {};

        this.rideIcon = L.ExtraMarkers.icon({ icon: 'fa-bicycle', markerColor: 'red', shape: 'circle', prefix: 'far' });
        this.locationIcon = L.ExtraMarkers.icon({ icon: 'fa-bicycle', markerColor: 'white', shape: 'circle', prefix: 'far' });
        this.subrideIcon = L.ExtraMarkers.icon({ icon: 'fa-bicycle', markerColor: 'green', shape: 'circle', prefix: 'far' });
        this.cityIcon = L.ExtraMarkers.icon({ icon: 'fa-university', markerColor: 'blue', shape: 'circle', prefix: 'far' });
        this.photoIcon = L.ExtraMarkers.icon({ icon: 'fa-camera', markerColor: 'yellow', shape: 'square', prefix: 'far' });

        this.createMap();
        this.setViewByProvidedData();
        this.setMarkerByProvidedData();
        this.setDraggableMarkerByProvidedData();
        this.addProvidedPolyline();
        this.queryApi();
        this.loadRide();
        this.loadPhotos();
        this.loadTracks();
        this.initEventListeners();
        this.disableInteraction();
    }

    createMap() {
        this.map = new L.map(this.element.id);
        this.element.map = this.map;

        const basemap = L.tileLayer('https://tiles.caldera.cc/wikimedia-intl/{z}/{x}/{y}.png', {
            attribution: 'Wikimedia maps beta | Map data &copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
        });
        basemap.addTo(this.map);
    }

    setViewByProvidedData() {
        const lat = this.element.dataset.mapCenterLatitude;
        const lng = this.element.dataset.mapCenterLongitude;
        const zoom = this.element.dataset.mapZoomlevel || 13;

        if (lat && lng && zoom) {
            this.map.setView(L.latLng(lat, lng), zoom);
        }
    }

    setDraggableMarkerByProvidedData() {
        const draggable = this.element.dataset.mapMarkerDraggable;
        const markerType = this.element.dataset.mapMarkerType;
        const lat = this.element.dataset.mapCenterLatitude;
        const lng = this.element.dataset.mapCenterLongitude;

        if (draggable && markerType && this.hasMarkerLatitudeTarget && this.hasMarkerLongitudeTarget) {
            const markerLatLng = L.latLng(this.markerLatitudeTarget.value || lat, this.markerLongitudeTarget.value || lng);

            const marker = L.marker(markerLatLng, {
                draggable: true,
                autoPan: true,
                icon: this.getIconForType(markerType)
            });

            marker.on('moveend', (event) => {
                const latLng = event.target.getLatLng();
                this.markerLatitudeTarget.value = latLng.lat;
                this.markerLongitudeTarget.value = latLng.lng;
            });

            marker.addTo(this.map);
        }
    }

    addProvidedPolyline() {
        const str = this.element.dataset.polyline;
        const color = this.element.dataset.polylineColor || '#3388ff';

        if (str) {
            const polyline = L.Polyline.fromEncoded(str, { color });
            polyline.addTo(this.map);
            this.map.fitBounds(polyline.getBounds());
        }
    }

    queryApi() {
        const url = this.element.dataset.apiQuery;
        const type = this.element.dataset.apiType || TYPE_RIDE;
        const icon = this.getIconForType(type);

        if (url) {
            fetch(url)
                .then(res => res.json())
                .then(list => {
                    const layer = L.featureGroup();
                    for (const item of list) {
                        if (!item.latitude || !item.longitude) continue;
                        const latLng = L.latLng(item.latitude, item.longitude);
                        L.marker(latLng, { icon }).addTo(layer);
                    }
                    layer.addTo(this.map);
                    this.map.fitBounds(layer.getBounds());
                })
                .catch(console.warn);
        }
    }

    loadRide() {
        const citySlug = this.element.dataset.citySlug;
        const rideIdentifier = this.element.dataset.rideIdentifier;

        if (!citySlug || !rideIdentifier) return;

        const url = `/api/${citySlug}/${rideIdentifier}`;

        fetch(url)
            .then(res => res.json())
            .then(ride => {
                const latLng = L.latLng(ride.latitude, ride.longitude);
                const marker = L.marker(latLng, { icon: this.rideIcon });
                this.map.setView(latLng, 10);
                marker.addTo(this.map);
            })
            .catch(console.warn);
    }

    loadPhotos() {
        const citySlug = this.element.dataset.citySlug;
        const rideIdentifier = this.element.dataset.rideIdentifier;

        if (!citySlug || !rideIdentifier) return;

        const url = `/api/${citySlug}/${rideIdentifier}/listPhotos`;

        fetch(url)
            .then(res => res.json())
            .then(photos => {
                const cluster = L.markerClusterGroup({
                    showCoverageOnHover: false,
                    iconCreateFunction: () => this.photoIcon
                });

                for (const p of photos) {
                    if (p.latitude && p.longitude) {
                        const latLng = L.latLng(p.latitude, p.longitude);
                        cluster.addLayer(L.marker(latLng, { icon: this.photoIcon }));
                    }
                }
                
                cluster.addTo(this.map);
            })
            .catch(console.warn);
    }

    loadTracks() {
        const citySlug = this.element.dataset.citySlug;
        const rideIdentifier = this.element.dataset.rideIdentifier;

        if (!citySlug || !rideIdentifier) return;

        const url = `/api/${citySlug}/${rideIdentifier}/listTracks`;

        fetch(url)
            .then(res => res.json())
            .then(tracks => {
                const layer = L.featureGroup();
                for (const track of tracks) {
                    const red = track.colorRed ?? 0;
                    const green = track.colorGreen ?? 0;
                    const blue = track.colorBlue ?? 0;

                    const color = `rgb(${red}, ${green}, ${blue})`;

                    const polyline = L.Polyline.fromEncoded(track.polylineString, { color });

                    polyline.addTo(layer);
                }
                layer.addTo(this.map);
                this.map.fitBounds(layer.getBounds());
            })
            .catch(console.warn);
    }

    getIconForType(type) {
        switch (type) {
            case TYPE_RIDE: return this.rideIcon;
            case TYPE_CITY: return this.cityIcon;
            case TYPE_PHOTO: return this.photoIcon;
            case TYPE_SUBRIDE: return this.subrideIcon;
            case TYPE_LOCATION: return this.locationIcon;
            default: return this.rideIcon;
        }
    }

    initEventListeners() {
        document.addEventListener('map-polyline-add', (e) => {
            this.addPolyline(e.polylineString, e.colorString, e.identifier);
        });

        document.addEventListener('map-polyline-update', (e) => {
            this.updatePolyline(e.polylineString, e.colorString, e.identifier);
        });

        document.addEventListener('map-clear', () => {
            this.map.eachLayer(layer => {
                if (!(layer instanceof L.TileLayer)) this.map.removeLayer(layer);
            });
        });
    }

    disableInteraction() {
        if (this.element.dataset.lockMap === 'true') {
            this.element.style.cursor = 'default';
            this.map.dragging.disable();
            this.map.touchZoom.disable();
            this.map.doubleClickZoom.disable();
            this.map.scrollWheelZoom.disable();
            this.map.boxZoom.disable();
            this.map.keyboard.disable();
            if (this.map.tap) this.map.tap.disable();

            const zoomControl = this.element.querySelector('.leaflet-control-zoom');
            if (zoomControl) zoomControl.remove();
        }
    }

    addPolyline(polylineString, colorString, identifier) {
        const polyline = L.Polyline.fromEncoded(polylineString, { color: colorString });
        polyline.addTo(this.map);
        this.polylineList[identifier] = polyline;
        this.map.fitBounds(polyline.getBounds());
    }

    updatePolyline(polylineString, colorString, identifier) {
        if (this.polylineList[identifier]) {
            const polyline = L.Polyline.fromEncoded(polylineString, { color: colorString });
            const latLngList = polyline.getLatLngs();
            this.polylineList[identifier].setLatLngs(latLngList);
            this.map.fitBounds(polyline.getBounds());
        }
    }

    setMarkerByProvidedData() {
        const draggable = this.element.dataset.mapMarkerDraggable;
        const markerType = this.element.dataset.mapMarkerType;
        const markerLatitude = this.element.dataset.mapMarkerLatitude;
        const markerLongitude = this.element.dataset.mapMarkerLongitude;

        if (!draggable && markerLatitude && markerLongitude && markerType) {
            const markerLatLng = L.latLng(markerLatitude, markerLongitude);

            const marker = L.marker(markerLatLng, {
                autoPan: true,
                icon: this.getIconForType(markerType)
            });

            marker.addTo(this.map);
        }
    }
}
