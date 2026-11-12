import BaseMapController from './base_map_controller';
import L from 'leaflet';

// Eine Farbe je Tourart — dieselbe am Marker, in der Liste und am Filter,
// damit der Filter zugleich die Legende ist.
const FARBEN = {
    CRITICAL_MASS: '#d6336c',
    KIDICAL_MASS: '#f08c00',
    NIGHT_RIDE: '#3b5bdb',
    LUNCH_RIDE: '#2f9e44',
    DAWN_RIDE: '#e8590c',
    DUSK_RIDE: '#7048e8',
    DEMONSTRATION: '#c92a2a',
    ALLEYCAT: '#0c8599',
    TOUR: '#5c940d',
    EVENT: '#495057',
};
const FARBE_OHNE_ART = '#868e96';

const PLATZHALTER = '0000-00-00';

// Die Übersichtskarte: alle Touren eines Tages, links als Liste, rechts als Marker.
//
// Der Tagesbestand kommt in einem Stueck (hoechstens ein paar hundert
// Eintraege). Suche, „nur aktive“, Tourart und Kartenausschnitt filtern danach
// im Browser; nur ein anderes Datum fragt den Server.
//
// Die Schreibweise der naechsten Zeile ist vorgegeben: Der
// lazy-controller-loader erkennt genau diesen einzeiligen Kommentar.
/* stimulusFetch: 'lazy' */
export default class extends BaseMapController {
    static targets = ['map', 'list', 'visible', 'total', 'date', 'range', 'period', 'search', 'active', 'type', 'typeLabel'];
    static values = {
        ...BaseMapController.values,
        urlTemplate: String,
    };

    get mapElement() {
        return this.mapTarget;
    }

    connect() {
        this.rides = [];
        this.markers = new Map();
        this.groups = [];
        this.layer = null;
        this.anfrage = 0;

        super.connect();

        this.layer = L.layerGroup().addTo(this.map);
        this.restoreView();
        this.addLocateControl();
        this.markTypeFilters();

        this.map.on('moveend', () => {
            this.renderList();
            this.updateAddress();
        });

        this.load();
    }

    // ---------- Daten ----------

    async load() {
        const period = this.period();

        if (!period) {
            return;
        }

        this.periodTarget.textContent = period.from === period.until
            ? ''
            : `${this.shortDate(period.from)} – ${this.shortDate(period.until)}`;

        // Wer schnell blaettert, loest mehrere Anfragen aus; nur die letzte zaehlt.
        const anfrage = ++this.anfrage;
        let url = this.urlTemplateValue.replace(PLATZHALTER, period.from);

        if (period.until !== period.from) {
            url += `/${period.until}`;
        }

        let json;

        try {
            json = await this.loadJson(url);
        } catch (e) {
            console.warn('[day-map] Touren konnten nicht geladen werden', e);
            json = { rides: [] };
        }

        if (anfrage !== this.anfrage || !this.map) {
            return;
        }

        this.rides = Array.isArray(json.rides) ? json.rides : [];
        this.buildMarkers();
        this.filter();
    }

    // Erster und letzter Tag des Zeitraums um das gewaehlte Datum: der Tag
    // selbst, seine Woche von Montag bis Sonntag oder sein Kalendermonat.
    // Gerechnet wird in UTC, sonst verschluckt die Zeitumstellung einen Tag.
    period() {
        const parts = this.dateParts();

        if (!parts) {
            return null;
        }

        const [year, month, day] = parts;
        const iso = (date) => date.toISOString().slice(0, 10);

        if ('week' === this.rangeTarget.value) {
            const weekday = (new Date(Date.UTC(year, month - 1, day)).getUTCDay() + 6) % 7;

            return {
                from: iso(new Date(Date.UTC(year, month - 1, day - weekday))),
                until: iso(new Date(Date.UTC(year, month - 1, day - weekday + 6))),
            };
        }

        if ('month' === this.rangeTarget.value) {
            return {
                from: iso(new Date(Date.UTC(year, month - 1, 1))),
                until: iso(new Date(Date.UTC(year, month, 0))),
            };
        }

        return { from: this.dateTarget.value, until: this.dateTarget.value };
    }

    dateParts() {
        const match = this.dateTarget.value.match(/^(\d{4})-(\d{2})-(\d{2})$/);

        return match ? match.slice(1).map(Number) : null;
    }

    isRange() {
        return 'day' !== this.rangeTarget.value;
    }

    // „Fr 27.11.“ — der Wochentag gehoert dazu, weil sich fast alles nach ihm richtet.
    shortDate(isoDate) {
        const [year, month, day] = isoDate.split('-').map(Number);
        const weekday = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'][new Date(Date.UTC(year, month - 1, day)).getUTCDay()];

        return `${weekday} ${String(day).padStart(2, '0')}.${String(month).padStart(2, '0')}.`;
    }

    // Ein Marker je Ort, nicht je Tour: Ueber einen Monat faehrt dieselbe
    // Stadt mehrmals vom selben Treffpunkt, und uebereinanderliegende Marker
    // liessen nur den obersten erreichen.
    buildMarkers() {
        this.layer.clearLayers();
        this.markers.clear();
        this.groups = [];

        const byPlace = new Map();

        for (const ride of this.rides) {
            const key = `${ride.latitude.toFixed(5)}/${ride.longitude.toFixed(5)}`;
            let group = byPlace.get(key);

            if (!group) {
                group = {
                    rides: [],
                    shown: [],
                    marker: L.circleMarker([ride.latitude, ride.longitude], { radius: 8, color: '#fff', weight: 2 }),
                };
                group.marker.bindPopup(() => this.popupFor(group));
                byPlace.set(key, group);
                this.groups.push(group);
            }

            group.rides.push(ride);
            this.markers.set(ride, group.marker);
        }
    }

    // ---------- Filter ----------

    filter() {
        // Mehrere Woerter muessen alle vorkommen, egal in welchem Feld:
        // „kidical hamburg“ findet die Kidical Mass in Hamburg.
        const words = this.normalize(this.searchTarget.value).split(/\s+/).filter(Boolean);
        const onlyActive = this.activeTarget.checked;
        const types = this.typeTargets.filter((box) => box.checked).map((box) => box.value);

        this.updateTypeLabel();

        this.filtered = this.rides.filter((ride) => {
            if (onlyActive && !ride.active) {
                return false;
            }

            if (types.length && !types.includes(ride.type)) {
                return false;
            }

            if (words.length) {
                if (undefined === ride.haystack) {
                    ride.haystack = this.normalize(ride.text || [ride.city, ride.title, ride.location].filter(Boolean).join(' '));
                }

                if (!words.every((word) => ride.haystack.includes(word))) {
                    return false;
                }
            }

            return true;
        });

        const wanted = new Set(this.filtered);

        for (const group of this.groups) {
            group.shown = group.rides.filter((ride) => wanted.has(ride));

            if (!group.shown.length) {
                this.layer.removeLayer(group.marker);
                continue;
            }

            // Farbe und Deckkraft richten sich nach dem, was hier tatsaechlich faehrt.
            const live = group.shown.filter((ride) => !ride.cancelled);
            const lead = live[0] || group.shown[0];

            group.marker.setStyle({
                fillColor: live.length ? this.colorOf(lead) : FARBE_OHNE_ART,
                fillOpacity: live.some((ride) => ride.active) ? 0.95 : 0.45,
            });
            group.marker.addTo(this.layer);
        }

        this.renderList();
        this.updateAddress();
    }

    // Gross-/Kleinschreibung und Akzente sollen bei der Suche keine Rolle
    // spielen: „munchen“ findet München, „malaga“ findet Málaga.
    normalize(text) {
        return String(text || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[̀-ͯ]/g, '')
            .trim();
    }

    // ---------- Liste ----------

    renderList() {
        if (!this.map) {
            return;
        }

        const bounds = this.map.getBounds();
        const visible = (this.filtered || [])
            .filter((ride) => bounds.contains([ride.latitude, ride.longitude]))
            .sort(this.isRange()
                // Ueber mehrere Tage zaehlt zuerst, wann etwas ist; an einem Tag, wo.
                ? (a, b) => a.date.localeCompare(b.date) || a.time.localeCompare(b.time) || a.city.localeCompare(b.city, 'de')
                : (a, b) => a.city.localeCompare(b.city, 'de') || a.time.localeCompare(b.time));

        this.listTarget.replaceChildren(...visible.map((ride) => this.listItemFor(ride)));
        this.visibleTarget.textContent = visible.length;
        this.totalTarget.textContent = this.rides.length;
    }

    listItemFor(ride) {
        const item = document.createElement('li');
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'day-map__entry';
        button.classList.toggle('day-map__entry--inactive', !ride.active);
        button.classList.toggle('day-map__entry--cancelled', ride.cancelled);
        button.addEventListener('click', () => this.focus(ride));
        button.addEventListener('mouseenter', () => this.highlight(ride, true));
        button.addEventListener('mouseleave', () => this.highlight(ride, false));

        button.append(
            this.dot(this.colorOf(ride)),
            this.span('day-map__entry-time', this.isRange() ? `${this.shortDate(ride.date)} ${ride.time}` : ride.time),
            this.span('day-map__entry-city', ride.city),
            this.span('day-map__entry-detail', ride.location || ride.title || '')
        );

        item.append(button);

        return item;
    }

    focus(ride) {
        const marker = this.markers.get(ride);

        if (!marker) {
            return;
        }

        this.map.setView(marker.getLatLng(), Math.max(this.map.getZoom(), 12));
        marker.openPopup();
    }

    highlight(ride, on) {
        const marker = this.markers.get(ride);

        if (marker) {
            marker.setRadius(on ? 12 : 8);

            if (on) {
                marker.bringToFront();
            }
        }
    }

    popupFor(group) {
        const box = document.createElement('div');
        box.className = 'day-map__popup';

        const rides = [...group.shown].sort((a, b) => a.date.localeCompare(b.date) || a.time.localeCompare(b.time));

        for (const ride of rides) {
            const entry = document.createElement('div');
            entry.className = 'day-map__popup-ride';

            const city = document.createElement('strong');
            city.textContent = ride.city;
            entry.append(city);

            if (ride.title) {
                entry.append(this.span('day-map__popup-title', ride.title));
            }

            const line = [`${ride.time} Uhr`];

            if (this.isRange()) {
                line.unshift(this.shortDate(ride.date));
            }

            if (ride.cancelled) {
                line.push('abgesagt');
            }

            if (ride.location) {
                line.push(ride.location);
            }

            entry.append(this.span('day-map__popup-line', line.join(' · ')));

            if (ride.url) {
                const link = document.createElement('a');
                link.href = ride.url;
                link.textContent = 'Zur Tour';
                entry.append(link);
            }

            box.append(entry);
        }

        return box;
    }

    span(className, text) {
        const el = document.createElement('span');
        el.className = className;
        el.textContent = text;

        return el;
    }

    dot(color) {
        const el = document.createElement('i');
        el.className = 'day-map__dot';
        el.style.backgroundColor = color;
        el.setAttribute('aria-hidden', 'true');

        return el;
    }

    colorOf(ride) {
        return FARBEN[ride.type] || FARBE_OHNE_ART;
    }

    // Der Knopf des Aufklappfelds sagt, was gewaehlt ist — sonst muesste man
    // es oeffnen, um zu sehen, warum Touren fehlen.
    updateTypeLabel() {
        if (!this.hasTypeLabelTarget) {
            return;
        }

        const label = this.typeLabelTarget;
        const chosen = this.typeTargets.filter((box) => box.checked);

        if (0 === chosen.length) {
            label.textContent = label.dataset.all;
        } else if (1 === chosen.length) {
            const text = chosen[0].parentElement && chosen[0].parentElement.querySelector('label');
            label.textContent = text ? text.textContent.trim() : chosen[0].value;
        } else {
            label.textContent = `${chosen.length} ${label.dataset.many}`;
        }
    }

    markTypeFilters() {
        for (const box of this.typeTargets) {
            const label = box.parentElement && box.parentElement.querySelector('label');

            if (label && !label.querySelector('.day-map__dot')) {
                label.prepend(this.dot(FARBEN[box.value] || FARBE_OHNE_ART));
            }
        }
    }

    // ---------- Datum ----------

    submit(event) {
        event.preventDefault();
    }

    changeDate() {
        this.load();
    }

    previousDay() {
        this.step(-1);
    }

    nextDay() {
        this.step(1);
    }

    // Blaettert um einen ganzen Zeitraum: einen Tag, eine Woche, einen Monat.
    step(direction) {
        const parts = this.dateParts();

        if (!parts) {
            return;
        }

        const [year, month, day] = parts;
        let date;

        if ('month' === this.rangeTarget.value) {
            // Der 31. bleibt im Zielmonat, statt in den uebernaechsten zu rutschen.
            const lastDay = new Date(Date.UTC(year, month + direction, 0)).getUTCDate();
            date = new Date(Date.UTC(year, month - 1 + direction, Math.min(day, lastDay)));
        } else {
            date = new Date(Date.UTC(year, month - 1, day + direction * ('week' === this.rangeTarget.value ? 7 : 1)));
        }

        this.dateTarget.value = date.toISOString().slice(0, 10);
        this.load();
    }

    // ---------- Standort ----------

    addLocateControl() {
        const LocateControl = L.Control.extend({
            options: { position: 'topleft' },
            onAdd: () => {
                const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                const button = L.DomUtil.create('a', 'day-map__locate', container);
                button.href = '#';
                button.title = 'Meinen Standort anzeigen';
                button.setAttribute('role', 'button');
                button.setAttribute('aria-label', button.title);
                button.innerHTML = '<i class="far fa-location-arrow" aria-hidden="true"></i>';

                L.DomEvent.disableClickPropagation(container);
                L.DomEvent.on(button, 'click', (e) => {
                    L.DomEvent.preventDefault(e);
                    button.classList.add('day-map__locate--busy');
                    this.map.locate({ setView: true, maxZoom: 11, enableHighAccuracy: false, timeout: 10000 });
                });

                this.locateButton = button;

                return container;
            },
        });

        new LocateControl().addTo(this.map);

        this.map.on('locationfound', (e) => {
            this.locateButton.classList.remove('day-map__locate--busy');

            if (this.position) {
                this.position.remove();
            }

            this.position = L.layerGroup([
                L.circle(e.latlng, { radius: e.accuracy, color: '#1c7ed6', weight: 1, fillOpacity: 0.12, interactive: false }),
                L.circleMarker(e.latlng, { radius: 6, color: '#fff', weight: 2, fillColor: '#1c7ed6', fillOpacity: 1, interactive: false }),
            ]).addTo(this.map);
        });

        this.map.on('locationerror', () => {
            this.locateButton.classList.remove('day-map__locate--busy');
        });
    }

    // ---------- Adresse ----------

    // Der Kartenausschnitt steht als #zoom/breite/laenge in der Adresse.
    restoreView() {
        const match = window.location.hash.match(/^#(\d+(?:\.\d+)?)\/(-?\d+(?:\.\d+)?)\/(-?\d+(?:\.\d+)?)$/);

        if (!match) {
            return;
        }

        const [zoom, lat, lng] = match.slice(1).map(Number);

        if (Math.abs(lat) <= 90 && Math.abs(lng) <= 180 && zoom <= 19) {
            this.map.setView([lat, lng], zoom);
        }
    }

    updateAddress() {
        if (!this.map) {
            return;
        }

        const params = new URLSearchParams();
        params.set('date', this.dateTarget.value);

        if (this.isRange()) {
            params.set('range', this.rangeTarget.value);
        }

        const search = this.searchTarget.value.trim();

        if (search) {
            params.set('q', search);
        }

        if (!this.activeTarget.checked) {
            params.set('active', '0');
        }

        for (const box of this.typeTargets) {
            if (box.checked) {
                params.append('type[]', box.value);
            }
        }

        const center = this.map.getCenter();
        const hash = `#${this.map.getZoom()}/${center.lat.toFixed(4)}/${center.lng.toFixed(4)}`;

        window.history.replaceState(null, '', `${window.location.pathname}?${params.toString()}${hash}`);
    }
}
