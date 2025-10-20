import { Controller } from '@hotwired/stimulus';
import L from 'leaflet';
import Handlebars from 'handlebars';
import 'polyline-encoded';
import 'leaflet.markercluster';
import 'leaflet-extra-markers';

const toDate = (d) => {
    if (d == null) return null;
    if (d instanceof Date) return d;
    if (typeof d === 'number') return new Date(d > 2e10 ? d : d * 1000);
    return new Date(d);
};
const getCitySlug = (city) => city?.main_slug?.slug || city?.slug || (Array.isArray(city?.slugs) && city.slugs[0]?.slug) || 'unknown';
const getDateTimeField = (i) => i?.date_time ?? i?.dateTime ?? null;

Handlebars.registerHelper('formatYmd', (i) => {
    const d = toDate(getDateTimeField(i));
    if (!d || isNaN(d)) return '';
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
});
Handlebars.registerHelper('formatDateLocal', (i) => {
    const d = toDate(getDateTimeField(i));
    if (!d || isNaN(d)) return '';
    const tz = i?.city?.timezone || 'Europe/Berlin';
    return new Intl.DateTimeFormat('de-DE', { timeZone: tz, year: 'numeric', month: '2-digit', day: '2-digit' }).format(d);
});
Handlebars.registerHelper('formatTimeLocal', (i) => {
    const d = toDate(getDateTimeField(i));
    if (!d || isNaN(d)) return '';
    const tz = i?.city?.timezone || 'Europe/Berlin';
    return new Intl.DateTimeFormat('de-DE', { timeZone: tz, hour: '2-digit', minute: '2-digit' }).format(d);
});
Handlebars.registerHelper('rideUrl', (i) => {
    const citySlug = getCitySlug(i?.city);
    const tail = (i?.slug && i.slug.length) ? i.slug : Handlebars.helpers.formatYmd(i);
    return `/${citySlug}/${tail}`;
});
Handlebars.registerHelper('nl2br', (t) => {
    if (t == null) return '';
    const s = String(t).replace(/\r\n|\r|\n/g, '<br>');
    return new Handlebars.SafeString(s);
});

export default class extends Controller {
    connect() {
        this.polylineList = {};
        this._initIcons();
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
        this.map = L.map(this.element, { zoomControl: true });
        this.element.map = this.map;
        const basemap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' });
        basemap.addTo(this.map);
        this.map.setView([51.1657, 10.4515], 6);
    }

    setViewByProvidedData() {
        const lat = this._num(this.element.dataset.mapCenterLatitude);
        const lng = this._num(this.element.dataset.mapCenterLongitude);
        const zoom = this._int(this.element.dataset.mapZoomlevel, 13);
        if (lat != null && lng != null) this.map.setView([lat, lng], zoom);
    }

    setDraggableMarkerByProvidedData() {
        const { mapMarkerDraggable, mapMarkerType, mapMarkerLatitudeTarget, mapMarkerLongitudeTarget } = this.element.dataset;
        if (mapMarkerDraggable !== 'true' || !mapMarkerType) return;
        const latInput = document.getElementById(mapMarkerLatitudeTarget);
        const lngInput = document.getElementById(mapMarkerLongitudeTarget);
        if (!latInput || !lngInput) return;
        const defaultLat = this._num(this.element.dataset.mapMarkerLatitude, this._num(this.element.dataset.mapCenterLatitude, this._num(latInput.value)));
        const defaultLng = this._num(this.element.dataset.mapMarkerLongitude, this._num(this.element.dataset.mapCenterLongitude, this._num(lngInput.value)));
        if (defaultLat == null || defaultLng == null) return;
        if (!latInput.value) latInput.value = defaultLat;
        if (!lngInput.value) lngInput.value = defaultLng;
        const marker = L.marker([this._num(latInput.value), this._num(lngInput.value)], {
            draggable: true,
            autoPan: true,
            icon: this.getIconForType(mapMarkerType)
        }).addTo(this.map);
        marker.on('moveend', (e) => {
            const p = e.target.getLatLng();
            latInput.value = p.lat;
            lngInput.value = p.lng;
        });
        if (this.element.dataset.mapFitToMarker === 'true') {
            const zoom = this._int(this.element.dataset.mapZoomlevel, 15);
            this.map.setView(marker.getLatLng(), zoom);
        }
    }

    setMarkerByProvidedData() {
        const { mapMarkerDraggable, mapMarkerType } = this.element.dataset;
        const lat = this._num(this.element.dataset.mapMarkerLatitude);
        const lng = this._num(this.element.dataset.mapMarkerLongitude);
        if (mapMarkerDraggable === 'true') return;
        if (lat == null || lng == null || !mapMarkerType) return;
        const marker = L.marker([lat, lng], { autoPan: true, icon: this.getIconForType(mapMarkerType) }).addTo(this.map);
        if (this.element.dataset.mapFitToMarker === 'true') {
            const zoom = this._int(this.element.dataset.mapZoomlevel, 15);
            this.map.setView(marker.getLatLng(), zoom);
        }
    }

    addProvidedPolyline() {
        const { polyline, polylineColor } = this.element.dataset;
        if (!polyline) return;
        const pl = L.Polyline.fromEncoded(polyline, { color: polylineColor || '#3388ff' }).addTo(this.map);
        this.map.fitBounds(pl.getBounds());
    }

    queryApi() {
        const apiQueryUrl = this.element.dataset.apiQuery;
        if (!apiQueryUrl) return;
        const type = this.element.dataset.apiType || 'ride';
        const icon = this.getIconForType(type) || new L.Icon.Default();
        let popupTpl = this.element.dataset.popupTemplate;
        const tplId = this.element.dataset.popupTemplateId;
        if (!popupTpl && tplId) {
            const el = document.getElementById(tplId);
            if (el) popupTpl = el.innerHTML.trim();
        }
        const popupTemplate = popupTpl ? Handlebars.compile(popupTpl) : null;
        const useCluster = this.element.dataset.useCluster === 'true';
        const layer = useCluster ? L.markerClusterGroup({ showCoverageOnHover: false }) : L.featureGroup();
        fetch(apiQueryUrl)
            .then((r) => r.json())
            .then((json) => {
                const list = Array.isArray(json) ? json : (json.items || json.results || []);
                if (!Array.isArray(list) || list.length === 0) return;
                for (const item of list) {
                    const lat = this._num(item.latitude ?? item.lat ?? item?.location?.lat);
                    const lng = this._num(item.longitude ?? item.lon ?? item.lng ?? item?.location?.lon);
                    if (!Number.isFinite(lat) || !Number.isFinite(lng)) continue;
                    const m = L.marker([lat, lng], { icon });
                    if (popupTemplate) m.bindPopup(popupTemplate(item));
                    layer.addLayer(m);
                }
                if (useCluster) this.map.addLayer(layer); else if (layer.getLayers().length) layer.addTo(this.map);
                if (layer.getLayers().length) this.map.fitBounds(layer.getBounds());
            })
            .catch((err) => console.warn(err));
    }

    initEventListeners() {
        document.addEventListener('map-polyline-add', (e) => {
            const { polylineString, colorString = '#3388ff', identifier } = e;
            const pl = L.Polyline.fromEncoded(polylineString, { color: colorString }).addTo(this.map);
            if (identifier) this.polylineList[identifier] = pl;
            this.map.fitBounds(pl.getBounds());
        });
        document.addEventListener('map-polyline-update', (e) => {
            const { polylineString, colorString = '#3388ff', identifier } = e;
            const existing = this.polylineList[identifier];
            if (!existing) return;
            const tmp = L.Polyline.fromEncoded(polylineString, { color: colorString });
            existing.setLatLngs(tmp.getLatLngs());
            existing.setStyle({ color: colorString });
            this.map.fitBounds(existing.getBounds());
        });
        document.addEventListener('map-clear', () => {
            this.map.eachLayer((layer) => {
                if (!(layer instanceof L.TileLayer)) this.map.removeLayer(layer);
            });
        });
    }

    disableInteraction() {
        if (this.element.dataset.lockMap === 'true') {
            const z = this.element.querySelector('.leaflet-control-zoom');
            if (z) z.remove();
            this.element.style.cursor = 'default';
            this.map.dragging.disable();
            this.map.touchZoom.disable();
            this.map.doubleClickZoom.disable();
            this.map.scrollWheelZoom.disable();
            this.map.boxZoom.disable();
            this.map.keyboard.disable();
            if (this.map.tap) this.map.tap.disable();
        }
    }

    _initIcons() {
        this.rideIcon = L.ExtraMarkers.icon({ icon: 'fa-bicycle', markerColor: 'red', shape: 'circle', prefix: 'fas' });
        this.locationIcon = L.ExtraMarkers.icon({ icon: 'fa-bicycle', markerColor: 'white', shape: 'circle', prefix: 'fas' });
        this.subrideIcon = L.ExtraMarkers.icon({ icon: 'fa-bicycle', markerColor: 'green', shape: 'circle', prefix: 'fas' });
        this.cityIcon = L.ExtraMarkers.icon({ icon: 'fa-university', markerColor: 'blue', shape: 'circle', prefix: 'fas' });
        this.photoIcon = L.ExtraMarkers.icon({ icon: 'fa-camera', markerColor: 'yellow', shape: 'square', prefix: 'fas' });
    }

    getIconForType(type) {
        switch (type) {
            case 'ride': return this.rideIcon;
            case 'city': return this.cityIcon;
            case 'photo': return this.photoIcon;
            case 'subride': return this.subrideIcon;
            case 'location': return this.locationIcon;
            default: return this.rideIcon;
        }
    }

    _num(v, f = null) {
        const n = parseFloat(v);
        return Number.isFinite(n) ? n : f;
    }

    _int(v, f = null) {
        const n = parseInt(v, 10);
        return Number.isFinite(n) ? n : f;
    }
}
