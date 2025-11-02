// controllers/query_map_controller.js
import BaseMapController from './base_map_controller';
import L from 'leaflet';
import Handlebars from 'handlebars';

export default class extends BaseMapController {
    static values = {
        ...BaseMapController.values,
        apiQuery: String,
        apiType: { type: String, default: 'generic' },
        popupTemplateId: String
    };

    async connect() {
        super.connect();
        await this.loadAndRender();
    }

    async loadAndRender() {
        const url = this.apiQueryValue || this.element.dataset.apiQuery;
        if (!url) return;

        let json;
        try {
            json = await this.loadJson(url);
        } catch (e) {
            console.warn('[query-map] API-Request fehlgeschlagen', e);
            return;
        }

        const items = this.normalizeList(json);
        if (!items.length) return;

        const group = L.featureGroup();
        const icon = this.getIconForType(this.apiTypeValue);
        const popupTemplate = this.getPopupTemplate();

        for (const item of items) {
            const { lat, lng } = this.getLatLngFromItem(item);
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) continue;

            const marker = L.marker([lat, lng], { icon });
            if (popupTemplate) marker.bindPopup(popupTemplate(item));
            group.addLayer(marker);
        }

        if (group.getLayers().length) {
            group.addTo(this.map);
            this.fitTo(group);
        }
    }

    normalizeList(json) {
        if (Array.isArray(json)) return json;
        if (Array.isArray(json.items)) return json.items;
        if (Array.isArray(json.results)) return json.results;
        return [];
    }

    getPopupTemplate() {
        const id = this.popupTemplateIdValue || this.element.dataset.popupTemplateId;
        if (!id) return null;
        const el = document.getElementById(id);
        if (!el) return null;
        const tpl = el.innerHTML.trim();
        return tpl ? Handlebars.compile(tpl) : null;
    }

    getLatLngFromItem(item) {
        const lat = parseFloat(item.latitude ?? item.lat ?? item?.location?.lat);
        const lng = parseFloat(item.longitude ?? item.lon ?? item.lng ?? item?.location?.lon);
        return { lat, lng };
    }

    getIconForType(type) {
        if (L.ExtraMarkers && typeof L.ExtraMarkers.icon === 'function') {
            const icons = {
                ride: ['fa-bicycle', 'red'],
                city: ['fa-university', 'blue'],
                photo: ['fa-camera', 'yellow'],
                location: ['fa-map-marker-alt', 'green']
            };
            const [icon, color] = icons[type] || ['fa-map-marker-alt', 'orange'];
            return L.ExtraMarkers.icon({ icon, markerColor: color, shape: 'circle', prefix: 'fas' });
        }
        return new L.Icon.Default();
    }
}
