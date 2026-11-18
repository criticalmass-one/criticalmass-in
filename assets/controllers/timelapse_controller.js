import { Controller } from '@hotwired/stimulus';
import L from 'leaflet';

export default class extends Controller {
    static values = {
        citySlug: String,
        rideIdentifier: String,
        rideDateTime: String,
    };

    static targets = [
        'startSection',
        'loaderSection',
        'controlSection',
        'trackNumber',
        'trackTotal',
        'timeClock',
        'timeElapsed',
        'speedSlider',
        'timeSlider',
    ];

    connect() {
        this._map = null;
        this._markerLayer = null;
        this._tracks = [];
        this._markers = [];
        this._animationId = null;
        this._playing = false;
        this._lastFrameTime = null;

        this._startTime = null;
        this._endTime = null;
        this._simulatedTime = null;

        this._speedFactor = 5;

        this._onMapReady = (e) => {
            this._map = e.detail.map;
        };
        document.addEventListener('ride-map:ready', this._onMapReady);

        const mapEl = document.querySelector('[data-controller*="ride-map"]');
        if (mapEl && mapEl.__leafletMap) {
            this._map = mapEl.__leafletMap;
        }
    }

    disconnect() {
        this._stopAnimation();
        this._removeMarkers();
        document.removeEventListener('ride-map:ready', this._onMapReady);
    }

    async start() {
        if (!this._map) {
            console.error('[timelapse] No map available');
            return;
        }

        this.startSectionTarget.style.display = 'none';
        this.loaderSectionTarget.style.display = 'block';

        try {
            const trackListUrl = `/api/${encodeURIComponent(this.citySlugValue)}/${encodeURIComponent(this.rideIdentifierValue)}/listTracks`;
            const res = await fetch(trackListUrl);
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const trackList = await res.json();

            if (!Array.isArray(trackList) || !trackList.length) {
                this.loaderSectionTarget.style.display = 'none';
                this.startSectionTarget.style.display = 'block';
                return;
            }

            this.trackTotalTarget.textContent = trackList.length;
            this._tracks = [];

            for (let i = 0; i < trackList.length; i++) {
                this.trackNumberTarget.textContent = i + 1;
                const track = trackList[i];

                const loadUrl = `/${encodeURIComponent(this.citySlugValue)}/${encodeURIComponent(this.rideIdentifierValue)}/timelapse/load/${track.id}`;
                const dataRes = await fetch(loadUrl);
                if (!dataRes.ok) continue;
                const points = await dataRes.json();

                if (!Array.isArray(points) || !points.length) continue;

                const color = this._trackColor(track);
                const timestamps = points.map((p) => new Date(p[0]).getTime());

                this._tracks.push({ id: track.id, points, timestamps, color });
            }

            if (!this._tracks.length) {
                this.loaderSectionTarget.style.display = 'none';
                this.startSectionTarget.style.display = 'block';
                return;
            }

            this._initTimeBounds();
            this._createMarkers();
            this._initSliders();

            this.loaderSectionTarget.style.display = 'none';
            this.controlSectionTarget.style.display = 'block';

            this._play();
        } catch (err) {
            console.error('[timelapse] Load failed', err);
            this.loaderSectionTarget.style.display = 'none';
            this.startSectionTarget.style.display = 'block';
        }
    }

    play() {
        this._play();
    }

    pause() {
        this._playing = false;
        this._lastFrameTime = null;
        if (this._animationId) {
            cancelAnimationFrame(this._animationId);
            this._animationId = null;
        }
    }

    stop() {
        this.pause();
        this._simulatedTime = this._startTime;
        this._updatePositions();
        this._updateUI();
    }

    stepForward() {
        this.pause();
        this._simulatedTime = Math.min(this._simulatedTime + 30000, this._endTime);
        this._updatePositions();
        this._updateUI();
    }

    stepBackward() {
        this.pause();
        this._simulatedTime = Math.max(this._simulatedTime - 30000, this._startTime);
        this._updatePositions();
        this._updateUI();
    }

    onSpeedChange() {
        this._speedFactor = parseFloat(this.speedSliderTarget.value) || 5;
    }

    onTimeChange() {
        const pct = parseFloat(this.timeSliderTarget.value) / 100;
        this._simulatedTime = this._startTime + pct * (this._endTime - this._startTime);
        this._updatePositions();
        this._updateUI();
    }

    // --- private ---

    _play() {
        if (this._playing) return;

        if (this._simulatedTime >= this._endTime) {
            this._simulatedTime = this._startTime;
        }

        this._playing = true;
        this._lastFrameTime = null;
        this._animationId = requestAnimationFrame((t) => this._tick(t));
    }

    _tick(now) {
        if (!this._playing) return;

        if (this._lastFrameTime !== null) {
            const realDelta = (now - this._lastFrameTime) / 1000;
            this._simulatedTime += realDelta * this._speedFactor * 60;

            if (this._simulatedTime >= this._endTime) {
                this._simulatedTime = this._endTime;
                this._updatePositions();
                this._updateUI();
                this.pause();
                return;
            }
        }

        this._lastFrameTime = now;
        this._updatePositions();
        this._updateUI();

        this._animationId = requestAnimationFrame((t) => this._tick(t));
    }

    _initTimeBounds() {
        this._startTime = Infinity;
        this._endTime = -Infinity;

        for (const track of this._tracks) {
            const first = track.timestamps[0];
            const last = track.timestamps[track.timestamps.length - 1];
            if (first < this._startTime) this._startTime = first;
            if (last > this._endTime) this._endTime = last;
        }

        this._simulatedTime = this._startTime;
    }

    _createMarkers() {
        this._markerLayer = L.layerGroup().addTo(this._map);
        this._markers = [];

        for (const track of this._tracks) {
            const first = track.points[0];
            const marker = L.circleMarker([first[1], first[2]], {
                radius: 6,
                color: track.color,
                fillColor: track.color,
                fillOpacity: 0.9,
                weight: 2,
            });
            marker.addTo(this._markerLayer);
            this._markers.push(marker);
        }
    }

    _updatePositions() {
        const t = this._simulatedTime;

        for (let i = 0; i < this._tracks.length; i++) {
            const track = this._tracks[i];
            const marker = this._markers[i];
            const ts = track.timestamps;

            if (t < ts[0] || t > ts[ts.length - 1]) {
                marker.setStyle({ opacity: 0, fillOpacity: 0 });
                continue;
            }

            marker.setStyle({ opacity: 1, fillOpacity: 0.9 });

            const idx = this._binarySearch(ts, t);

            if (idx >= ts.length - 1) {
                const last = track.points[ts.length - 1];
                marker.setLatLng([last[1], last[2]]);
                continue;
            }

            const t0 = ts[idx];
            const t1 = ts[idx + 1];
            const ratio = t1 === t0 ? 0 : (t - t0) / (t1 - t0);

            const p0 = track.points[idx];
            const p1 = track.points[idx + 1];
            const lat = p0[1] + ratio * (p1[1] - p0[1]);
            const lng = p0[2] + ratio * (p1[2] - p0[2]);

            marker.setLatLng([lat, lng]);
        }
    }

    _binarySearch(arr, value) {
        let lo = 0;
        let hi = arr.length - 1;

        while (lo < hi) {
            const mid = (lo + hi + 1) >>> 1;
            if (arr[mid] <= value) {
                lo = mid;
            } else {
                hi = mid - 1;
            }
        }

        return lo;
    }

    _updateUI() {
        const d = new Date(this._simulatedTime);
        const hh = String(d.getHours()).padStart(2, '0');
        const mm = String(d.getMinutes()).padStart(2, '0');
        this.timeClockTarget.textContent = `${hh}:${mm}`;

        const elapsed = Math.round((this._simulatedTime - this._startTime) / 60000);
        this.timeElapsedTarget.textContent = elapsed;

        if (this.hasTimeSliderTarget && this._endTime > this._startTime) {
            const pct = ((this._simulatedTime - this._startTime) / (this._endTime - this._startTime)) * 100;
            this.timeSliderTarget.value = Math.round(pct);
        }
    }

    _initSliders() {
        if (this.hasSpeedSliderTarget) {
            this.speedSliderTarget.value = this._speedFactor;
        }
        if (this.hasTimeSliderTarget) {
            this.timeSliderTarget.value = 0;
        }
    }

    _trackColor(track) {
        if (track.user) {
            const { color_red, color_green, color_blue } = track.user;
            const hex = (v) => Math.max(0, Math.min(255, v)).toString(16).padStart(2, '0');
            return `#${hex(color_red)}${hex(color_green)}${hex(color_blue)}`;
        }
        return '#3388ff';
    }

    _stopAnimation() {
        this._playing = false;
        this._lastFrameTime = null;
        if (this._animationId) {
            cancelAnimationFrame(this._animationId);
            this._animationId = null;
        }
    }

    _removeMarkers() {
        if (this._markerLayer && this._map) {
            this._map.removeLayer(this._markerLayer);
            this._markerLayer = null;
        }
        this._markers = [];
    }
}
