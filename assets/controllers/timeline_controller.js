import { Controller } from '@hotwired/stimulus';
import Handlebars from 'handlebars';

export default class extends Controller {
    static values = {
        apiUrl: String,
        initialLimit: { type: Number, default: 10 },
        batchSize: { type: Number, default: 20 },
    };

    static targets = ['items', 'loadMore', 'loading'];

    offset = 0;
    templates = {};

    connect() {
        this.compileTemplates();
        this.loadItems(this.initialLimitValue);
    }

    compileTemplates() {
        const userColumnEl = document.getElementById('timeline-user-column');
        if (userColumnEl) {
            Handlebars.registerPartial('userColumn', userColumnEl.innerHTML.trim());
        }

        const socialColumnEl = document.getElementById('timeline-social-column');
        if (socialColumnEl) {
            Handlebars.registerPartial('socialColumn', socialColumnEl.innerHTML.trim());
        }

        Handlebars.registerHelper('ifEquals', function(a, b, options) {
            return a === b ? options.fn(this) : options.inverse(this);
        });

        const types = [
            'cityCreated', 'cityEdit', 'rideComment', 'rideEdit',
            'ridePhoto', 'photoComment', 'rideParticipationEstimate',
            'rideTrack', 'thread', 'threadPost', 'socialNetworkFeedItem',
        ];

        for (const type of types) {
            const el = document.getElementById(`timeline-${type}-template`);

            if (el) {
                this.templates[type] = Handlebars.compile(el.innerHTML.trim());
            }
        }
    }

    renderItem(item) {
        const template = this.templates[item.type];

        if (!template) {
            console.warn(`[timeline] No template for type: ${item.type}`);
            return '';
        }

        return template(item);
    }

    async loadItems(limit) {
        this.showLoading();

        try {
            const url = `${this.apiUrlValue}?limit=${limit}&offset=${this.offset}`;
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();

            const html = data.items.map(item => this.renderItem(item)).join('');
            this.itemsTarget.insertAdjacentHTML('beforeend', html);
            this.offset += data.items.length;

            if (data.hasMore) {
                this.loadMoreTarget.classList.remove('d-none');
            } else {
                this.loadMoreTarget.classList.add('d-none');
            }

            if (this.offset === 0 && data.items.length === 0) {
                this.itemsTarget.innerHTML = '<p class="text-muted">Keine Eintr\u00e4ge vorhanden.</p>';
            }
        } catch (error) {
            this.renderError();
        }

        this.hideLoading();
    }

    loadMore() {
        this.loadItems(this.batchSizeValue);
    }

    renderError() {
        this.itemsTarget.innerHTML = `
            <div class="alert alert-danger" role="alert">
                Die Timeline konnte leider nicht geladen werden. Bitte versuche es sp\u00e4ter erneut.
            </div>`;
        this.loadMoreTarget.classList.add('d-none');
    }

    showLoading() {
        if (this.hasLoadingTarget) {
            this.loadingTarget.classList.remove('d-none');
        }
    }

    hideLoading() {
        if (this.hasLoadingTarget) {
            this.loadingTarget.classList.add('d-none');
        }
    }
}
