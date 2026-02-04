import QueryMapController from './query_map_controller';
import L from 'leaflet';
import Handlebars from 'handlebars';
import '@maplibre/maplibre-gl-leaflet';

export default class extends QueryMapController {
    static targets = ['sidebar', 'sidebarList', 'toggleBtn', 'map'];
    static values = {
        ...QueryMapController.values,
        sidebarItemTemplateId: String
    };

    items = [];
    markers = new Map();

    get mapElement() {
        return this.hasMapTarget ? this.mapTarget : this.element.querySelector('.explore-map') || this.element;
    }

    initMap() {
        const locked = this.hasLockMapValue && this.lockMapValue === 'true';

        this.map = L.map(this.mapElement, {
            zoomControl: !locked,
            maxBounds: [[180, -Infinity], [-180, Infinity]],
            maxBoundsViscosity: 1,
            minZoom: 1,
        });

        const lat = this.hasCenterLatitudeValue ? this.centerLatitudeValue : 51.1657;
        const lng = this.hasCenterLongitudeValue ? this.centerLongitudeValue : 10.4515;
        const zoom = this.zoomValue ?? 12;
        this.map.setView([lat, lng], zoom);

        if (this.vectorValue) {
            this._glLayer = L.maplibreGL({
                style: 'https://tiles.openfreemap.org/styles/liberty',
            }).addTo(this.map);
        } else {
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(this.map);
        }

        setTimeout(() => this.map.invalidateSize(), 80);
    }

    async connect() {
        await super.connect();
        this.map.on('moveend', () => this.syncSidebar());
        this.createSidebarControl();
    }

    createSidebarControl() {
        const SidebarControl = L.Control.extend({
            options: { position: 'topleft' },
            onAdd: () => {
                const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control explore-sidebar-control');
                const button = L.DomUtil.create('a', 'explore-sidebar-control__btn', container);
                button.href = '#';
                button.title = 'Städteliste anzeigen';
                button.innerHTML = '<i class="fa fa-list"></i>';
                button.setAttribute('role', 'button');

                L.DomEvent.disableClickPropagation(container);
                L.DomEvent.on(button, 'click', (e) => {
                    L.DomEvent.preventDefault(e);
                    this.toggleSidebar();
                });

                return container;
            }
        });

        this.sidebarControl = new SidebarControl();
        this.sidebarControl.addTo(this.map);
        this.sidebarControl.getContainer().style.display = 'none';
    }

    async loadAndRender() {
        const url = this.apiQueryValue || this.element.dataset.apiQuery;
        if (!url) {
            return;
        }

        let json;

        try {
            json = await this.loadJson(url);
        } catch (e) {
            console.warn('[explore-map] API-Request fehlgeschlagen', e);
            return;
        }

        this.items = this.normalizeList(json);

        if (!this.items.length) {
            return;
        }

        const group = L.featureGroup();
        const icon = this.getIconForType(this.apiTypeValue);
        const popupTemplate = this.getPopupTemplate();

        for (const item of this.items) {
            const { lat, lng } = this.getLatLngFromItem(item);

            if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                continue;
            }

            const marker = L.marker([lat, lng], { icon });

            if (popupTemplate) {
                marker.bindPopup(popupTemplate(item));
            }

            group.addLayer(marker);
            this.markers.set(item, marker);
        }

        if (group.getLayers().length) {
            group.addTo(this.map);
            this.fitTo(group);
        }

        this.syncSidebar();
    }

    syncSidebar() {
        if (!this.hasSidebarListTarget) {
            return;
        }

        const bounds = this.map.getBounds();
        const visibleItems = this.items.filter(item => {
            const { lat, lng } = this.getLatLngFromItem(item);
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                return false;
            }
            return bounds.contains([lat, lng]);
        });

        visibleItems.sort((a, b) => {
            const nameA = (a.title || a.name || '').toLowerCase();
            const nameB = (b.title || b.name || '').toLowerCase();
            return nameA.localeCompare(nameB);
        });

        const template = this.getSidebarItemTemplate();
        if (!template) {
            return;
        }

        this.sidebarListTarget.innerHTML = visibleItems
            .map(item => template(item))
            .join('');

        this.updateCounter(visibleItems.length);
    }

    getSidebarItemTemplate() {
        const id = this.sidebarItemTemplateIdValue || 'sidebar-item-template';
        const el = document.getElementById(id);

        if (!el) {
            return null;
        }

        const tpl = el.innerHTML.trim();
        return tpl ? Handlebars.compile(tpl) : null;
    }

    updateCounter(count) {
        const counterEl = this.element.querySelector('[data-counter]');
        if (counterEl) {
            counterEl.textContent = count;
        }
    }

    toggleSidebar() {
        if (!this.hasSidebarTarget) {
            return;
        }

        const isCollapsed = this.sidebarTarget.classList.toggle('explore-sidebar--collapsed');

        if (this.sidebarControl) {
            this.sidebarControl.getContainer().style.display = isCollapsed ? 'block' : 'none';
        }

        setTimeout(() => {
            this.map.invalidateSize();
        }, 350);
    }

    focusCity(event) {
        const lat = parseFloat(event.currentTarget.dataset.lat);
        const lng = parseFloat(event.currentTarget.dataset.lng);

        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
            return;
        }

        this.map.setView([lat, lng], 12);

        for (const [item, marker] of this.markers) {
            const itemLat = parseFloat(item.latitude ?? item.lat ?? item?.location?.lat);
            const itemLng = parseFloat(item.longitude ?? item.lon ?? item.lng ?? item?.location?.lon);

            if (Math.abs(itemLat - lat) < 0.0001 && Math.abs(itemLng - lng) < 0.0001) {
                marker.openPopup();
                break;
            }
        }
    }
}
