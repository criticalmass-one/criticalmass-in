import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        apiUrl: String,
        initialLimit: { type: Number, default: 10 },
        batchSize: { type: Number, default: 20 },
    };

    static targets = ['items', 'loadMore', 'loading'];

    offset = 0;

    connect() {
        this.loadItems(this.initialLimitValue);
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

            this.itemsTarget.insertAdjacentHTML('beforeend', data.items.join(''));
            this.offset += data.items.length;

            if (data.hasMore) {
                this.loadMoreTarget.classList.remove('d-none');
            } else {
                this.loadMoreTarget.classList.add('d-none');
            }

            if (this.offset === 0 && data.items.length === 0) {
                this.itemsTarget.innerHTML = '<p class="text-muted">Keine Einträge vorhanden.</p>';
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
                Die Timeline konnte leider nicht geladen werden. Bitte versuche es später erneut.
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
