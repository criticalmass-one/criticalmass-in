import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'results'];
    static values = {
        prefetchUrl: { type: String, default: '/search/prefetch' }
    }

    data = [];
    selectedIndex = -1;

    connect() {
        this.loadData();
        this.inputTarget.addEventListener('input', this.onInput.bind(this));
        this.inputTarget.addEventListener('keydown', this.onKeydown.bind(this));
        document.addEventListener('click', this.onClickOutside.bind(this));
    }

    disconnect() {
        document.removeEventListener('click', this.onClickOutside.bind(this));
    }

    async loadData() {
        try {
            const response = await fetch(this.prefetchUrlValue);
            this.data = await response.json();
        } catch (err) {
            console.warn('Search prefetch failed:', err);
        }
    }

    onInput(event) {
        const query = event.target.value.toLowerCase().trim();

        if (query.length < 1) {
            this.hideResults();
            return;
        }

        const matches = this.data.filter(item =>
            item.value.toLowerCase().includes(query)
        ).slice(0, 10);

        this.showResults(matches);
    }

    onKeydown(event) {
        if (!this.hasResultsTarget) return;

        const items = this.resultsTarget.querySelectorAll('.search-result-item');

        switch (event.key) {
            case 'ArrowDown':
                event.preventDefault();
                this.selectedIndex = Math.min(this.selectedIndex + 1, items.length - 1);
                this.updateSelection(items);
                break;
            case 'ArrowUp':
                event.preventDefault();
                this.selectedIndex = Math.max(this.selectedIndex - 1, 0);
                this.updateSelection(items);
                break;
            case 'Enter':
                if (this.selectedIndex >= 0 && items[this.selectedIndex]) {
                    const link = items[this.selectedIndex].querySelector('a');
                    if (link) window.location.href = link.href;
                }
                break;
            case 'Escape':
                this.hideResults();
                break;
        }
    }

    updateSelection(items) {
        items.forEach((item, index) => {
            item.classList.toggle('active', index === this.selectedIndex);
        });
    }

    showResults(matches) {
        if (!this.hasResultsTarget) return;

        this.selectedIndex = -1;

        if (matches.length === 0) {
            this.hideResults();
            return;
        }

        const html = matches.map(item => this.renderSuggestion(item)).join('');
        this.resultsTarget.innerHTML = html;
        this.resultsTarget.classList.remove('d-none');
    }

    hideResults() {
        if (this.hasResultsTarget) {
            this.resultsTarget.classList.add('d-none');
            this.resultsTarget.innerHTML = '';
        }
    }

    onClickOutside(event) {
        if (!this.element.contains(event.target)) {
            this.hideResults();
        }
    }

    renderSuggestion(data) {
        let html = '<div class="search-result-item p-2">';

        if (data.type === 'city') {
            html += `<a href="${data.url}"><i class="far fa-university"></i> ${data.value}</a>`;
        } else if (data.type === 'ride') {
            html += `<a href="${data.url}">`;
            html += `<div><i class="far fa-bicycle"></i> ${data.value}</div>`;
            if (data.meta?.location) {
                html += `<div class="small text-muted">${data.meta.location}</div>`;
            }
            if (data.meta?.dateTime) {
                const dateTime = new Date(data.meta.dateTime);
                html += `<div class="small text-muted">${dateTime.toLocaleDateString('de-DE')} ${dateTime.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' })} Uhr</div>`;
            }
            html += '</a>';
        } else if (data.type === 'content') {
            html += `<a href="${data.url}"><i class="far fa-file-text"></i> ${data.value}</a>`;
        }

        html += '</div>';
        return html;
    }
}
