// controllers/promotion_map_controller.js
import BaseMapController from './base_map_controller';
import L from 'leaflet';
import Handlebars from 'handlebars';

export default class extends BaseMapController {
    static values = {
        ...BaseMapController.values,
        apiQuery: String,
        apiType: { type: String, default: 'ride' },
        popupTemplateId: String
    };

    async connect() {
        // Karte aufbauen
        super.connect();

        // Daten laden
        await this.loadAndRender();
    }

    async loadAndRender() {
        const url = this.getApiUrl();
        if (!url) {
            console.warn('[promotion-map] keine data-api-query angegeben');
            return;
        }

        let json;
        try {
            json = await this.loadJson(url);
        } catch (e) {
            console.warn('[promotion-map] API-Request fehlgeschlagen', e);
            return;
        }

        const items = this.normalizeList(json);
        if (!items.length) {
            return;
        }

        const group = L.featureGroup();
        const icon = this.getIconForType(this.apiTypeValue);
        const popupTemplate = this.getPopupTemplate();

        for (const item of items) {
            const { lat, lng } = this.getLatLngFromItem(item);
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) continue;

            const marker = L.marker([lat, lng], { icon });

            if (popupTemplate) {
                const html = popupTemplate(item);
                marker.bindPopup(html);
            }

            marker.addTo(group);
        }

        if (group.getLayers().length) {
            group.addTo(this.map);
            this.fitTo(group);
        }
    }

    getApiUrl() {
        if (this.hasApiQueryValue) {
            return this.apiQueryValue;
        }
        const ds = this.element.dataset;
        return ds.apiQuery || null;
    }

    normalizeList(json) {
        if (!json) return [];
        if (Array.isArray(json)) return json;
        if (Array.isArray(json.items)) return json.items;
        if (Array.isArray(json.results)) return json.results;
        return [];
    }

    getPopupTemplate() {
        // zuerst: data-popup-template-id
        if (this.hasPopupTemplateIdValue) {
            const el = document.getElementById(this.popupTemplateIdValue);
            if (el) {
                const tpl = el.innerHTML.trim();
                if (tpl.length) {
                    return Handlebars.compile(tpl);
                }
            }
        }

        // fallback: gar kein popup
        return null;
    }

    getLatLngFromItem(item) {
        // viele APIs haben leicht andere Felder
        const lat =
            parseFloat(item.latitude) ??
            parseFloat(item.lat) ??
            parseFloat(item?.location?.lat);
        const lng =
            parseFloat(item.longitude) ??
            parseFloat(item.lon) ??
            parseFloat(item.lng) ??
            parseFloat(item?.location?.lon);

        return { lat, lng };
    }

    getIconForType(type) {
        // wenn ExtraMarkers da ist → hübsch
        if (L.ExtraMarkers && typeof L.ExtraMarkers.icon === 'function') {
            if (type === 'ride') {
                return L.ExtraMarkers.icon({
                    icon: 'fa-bicycle',
                    markerColor: 'red',
                    shape: 'circle',
                    prefix: 'fas'
                });
            }
            if (type === 'city') {
                return L.ExtraMarkers.icon({
                    icon: 'fa-university',
                    markerColor: 'blue',
                    shape: 'circle',
                    prefix: 'fas'
                });
            }
            if (type === 'photo') {
                return L.ExtraMarkers.icon({
                    icon: 'fa-camera',
                    markerColor: 'yellow',
                    shape: 'square',
                    prefix: 'fas'
                });
            }
            if (type === 'location') {
                return L.ExtraMarkers.icon({
                    icon: 'fa-map-marker-alt',
                    markerColor: 'green',
                    shape: 'circle',
                    prefix: 'fas'
                });
            }
            // Default für unbekannte Typen
            return L.ExtraMarkers.icon({
                icon: 'fa-map-marker-alt',
                markerColor: 'orange',
                shape: 'circle',
                prefix: 'fas'
            });
        }

        // Fallback: Standard-Leaflet-Marker
        return new L.Icon.Default();
    }
}
