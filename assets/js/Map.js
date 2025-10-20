import L from 'leaflet';
import Handlebars from 'Handlebars';
import polylineEncoded from 'polyline-encoded';
import markerCluster from 'leaflet.markercluster';
import extraMarkers from 'leaflet-extra-markers';
// kleine Utils
const toDate = (dateLike) => {
    if (dateLike == null) return null;
    if (dateLike instanceof Date) return dateLike;
    if (typeof dateLike === 'number') {
        // Sekunden -> ms
        return new Date(dateLike > 2e10 ? dateLike : dateLike * 1000);
    }
    return new Date(dateLike);
};

const getCitySlug = (city) => {
    return city?.main_slug?.slug || city?.slug || (Array.isArray(city?.slugs) && city.slugs[0]?.slug) || 'unknown';
};

const getDateTimeField = (item) => item?.date_time ?? item?.dateTime ?? null;

// Datum YYYY-MM-DD (für URLs)
Handlebars.registerHelper('formatYmd', function (item) {
    const d = toDate(getDateTimeField(item));
    if (!d || isNaN(d)) return '';
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
});

// Datum lokal formatiert (z. B. 21.09.2025)
Handlebars.registerHelper('formatDateLocal', function (item) {
    const d = toDate(getDateTimeField(item));
    if (!d || isNaN(d)) return '';
    const tz = item?.city?.timezone || 'Europe/Berlin';
    return new Intl.DateTimeFormat('de-DE', {
        timeZone: tz, year: 'numeric', month: '2-digit', day: '2-digit'
    }).format(d);
});

// Uhrzeit lokal formatiert (z. B. 15:30)
Handlebars.registerHelper('formatTimeLocal', function (item) {
    const d = toDate(getDateTimeField(item));
    if (!d || isNaN(d)) return '';
    const tz = item?.city?.timezone || 'Europe/Berlin';
    return new Intl.DateTimeFormat('de-DE', {
        timeZone: tz, hour: '2-digit', minute: '2-digit'
    }).format(d);
});

// URL: /{citySlug}/{slug || YYYY-MM-DD(date_time)}
Handlebars.registerHelper('rideUrl', function (item) {
    const citySlug = getCitySlug(item?.city);
    const tail = (item?.slug && item.slug.length) ? item.slug : Handlebars.helpers.formatYmd(item);
    return `/${citySlug}/${tail}`;
});

// \n -> <br> für Beschreibung (falls du sie irgendwo nutzt)
Handlebars.registerHelper('nl2br', function (text) {
    if (text == null) return '';
    const s = String(text).replace(/\r\n|\r|\n/g, '<br>');
    return new Handlebars.SafeString(s);
});
export default class Map {
    mapContainer;
    map;
    polylineList = [];

    rideIcon = L.ExtraMarkers.icon({
        icon: 'fa-bicycle',
        markerColor: 'red',
        shape: 'circle',
        prefix: 'fas'
    });
    locationIcon = L.ExtraMarkers.icon({
        icon: 'fa-bicycle',
        markerColor: 'white',
        shape: 'circle',
        prefix: 'fas'
    });
    subrideIcon = L.ExtraMarkers.icon({
        icon: 'fa-bicycle',
        markerColor: 'green',
        shape: 'circle',
        prefix: 'fas'
    });
    cityIcon = L.ExtraMarkers.icon({
        icon: 'fa-university',
        markerColor: 'blue',
        shape: 'circle',
        prefix: 'fas'
    });
    photoIcon = L.ExtraMarkers.icon({
        icon: 'fa-camera',
        markerColor: 'yellow',
        shape: 'square',
        prefix: 'fas'
    });

    constructor(mapContainer, options) {
        this.mapContainer = mapContainer;
        const defaults = {};
        this.settings = { ...defaults, ...options };

        this.createMap();
        this.setViewByProvidedData();
        this.setMarkerByProvidedData();
        this.setDraggableMarkerByProvidedData();
        this.addProvidedPolyline();
        this.queryApi();
        this.initEventListeners();
        this.disableInteraction();
    }

    createMap() {
        this.map = L.map(this.mapContainer, { zoomControl: true });
        this.mapContainer.map = this.map;

        const basemap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        });
        basemap.addTo(this.map);

        // Fallback-View falls keine Daten
        if (!this.map._loaded) {
            this.map.setView([51.1657, 10.4515], 6); // Deutschland Mitte
        }
    }

    setViewByProvidedData() {
        const { mapCenterLatitude, mapCenterLongitude, mapZoomlevel } = this.mapContainer.dataset;
        if (mapCenterLatitude && mapCenterLongitude && mapZoomlevel) {
            const mapCenter = L.latLng(mapCenterLatitude, mapCenterLongitude);
            this.map.setView(mapCenter, mapZoomlevel);
        }
    }

    setDraggableMarkerByProvidedData() {
        const { mapMarkerDraggable, mapMarkerType, mapCenterLatitude, mapCenterLongitude, mapMarkerLatitudeTarget, mapMarkerLongitudeTarget } = this.mapContainer.dataset;

        const markerLatitudeTarget = document.getElementById(mapMarkerLatitudeTarget);
        const markerLongitudeTarget = document.getElementById(mapMarkerLongitudeTarget);

        if (mapMarkerDraggable && markerLatitudeTarget && markerLongitudeTarget && mapMarkerType) {
            const markerLatLng = L.latLng(
                markerLatitudeTarget.value || mapCenterLatitude,
                markerLongitudeTarget.value || mapCenterLongitude
            );

            const options = {
                draggable: true,
                autoPan: true,
                icon: this.getIconForType(mapMarkerType)
            };

            const marker = L.marker(markerLatLng, options);
            marker.addTo(this.map);

            marker.on('moveend', (event) => {
                const latLng = event.target.getLatLng();
                markerLatitudeTarget.value = latLng.lat;
                markerLongitudeTarget.value = latLng.lng;
            });
        }
    }

    setMarkerByProvidedData() {
        const { mapMarkerDraggable, mapMarkerType, mapMarkerLatitude, mapMarkerLongitude } = this.mapContainer.dataset;

        if (!mapMarkerDraggable && mapMarkerLatitude && mapMarkerLongitude && mapMarkerType) {
            const markerLatLng = L.latLng(mapMarkerLatitude, mapMarkerLongitude);
            const options = {
                autoPan: true,
                icon: this.getIconForType(mapMarkerType)
            };
            const marker = L.marker(markerLatLng, options);
            marker.addTo(this.map);
        }
    }

    addProvidedPolyline() {
        const { polyline, polylineColor } = this.mapContainer.dataset;
        if (polyline && polylineColor) {
            const pl = L.Polyline.fromEncoded(polyline, { color: polylineColor });
            pl.addTo(this.map);
            this.map.fitBounds(pl.getBounds());
        }
    }

    getIconForType(type) {
        if (type === 'ride') return this.rideIcon;
        if (type === 'city') return this.cityIcon;
        if (type === 'photo') return this.photoIcon;
        if (type === 'subride') return this.subrideIcon;
        if (type === 'location') return this.locationIcon;
    }

    /** =========================
     *  API + Popup-Templates
     *  ========================= */
    queryApi() {
        const apiQueryUrl = this.mapContainer.dataset.apiQuery;
        const type = this.mapContainer.dataset.apiType || 'ride';
        const icon = this.getIconForType(type) || new L.Icon.Default();

        let popupTpl = this.mapContainer.dataset.popupTemplate;
        const tplId = this.mapContainer.dataset.popupTemplateId;

        if (!popupTpl && tplId) {
            const el = document.getElementById(tplId);
            if (el) popupTpl = el.innerHTML.trim();
        }

        if (!apiQueryUrl) return;

        fetch(apiQueryUrl)
            .then(r => r.json())
            .then(json => {
                const list = Array.isArray(json) ? json : (json.items || json.results || []);

                if (!Array.isArray(list) || list.length === 0) return;

                const layer = L.featureGroup();

                const popupTemplate = popupTpl ? Handlebars.compile(popupTpl) : null;

                for (const item of list) {
                    const lat = parseFloat(item.latitude ?? item.lat ?? item?.location?.lat);
                    const lng = parseFloat(item.longitude ?? item.lon ?? item.lng ?? item?.location?.lon);
                    if (!Number.isFinite(lat) || !Number.isFinite(lng)) continue;

                    const marker = L.marker([lat, lng], { icon });

                    if (popupTemplate) {
                        marker.bindPopup(popupTemplate(item));
                    }

                    marker.addTo(layer);
                }

                if (layer.getLayers().length) {
                    layer.addTo(this.map);
                    this.map.fitBounds(layer.getBounds());
                }
            })
            .catch(err => console.warn(err));
    }

    initEventListeners() {
        // hier kannst du deine Event-Listener ergänzen
    }

    disableInteraction() {
        if (this.mapContainer.dataset.lockMap) {
            const z = this.mapContainer.querySelector('.leaflet-control-zoom');
            if (z) z.remove();
            this.mapContainer.style.cursor = 'default';
            this.map.dragging.disable();
            this.map.touchZoom.disable();
            this.map.doubleClickZoom.disable();
            this.map.scrollWheelZoom.disable();
            this.map.boxZoom.disable();
            this.map.keyboard.disable();
            if (this.map.tap) this.map.tap.disable();
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.map').forEach(function (mapContainer) {
        new Map(mapContainer);
    });
});
