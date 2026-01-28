import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'results'];
    static values = {
        prefetchUrl: { type: String, default: '/search/prefetch' }
    }

    searchResults = [];
    selectedIndex = -1;
    isLoading = true;
    boundOnClickOutside = null;

    connect() {
        this.boundOnClickOutside = this.onClickOutside.bind(this);
        this.loadData();
        this.inputTarget.addEventListener('input', this.onInput.bind(this));
        this.inputTarget.addEventListener('keydown', this.onKeydown.bind(this));
        this.inputTarget.addEventListener('focus', this.onFocus.bind(this));
        document.addEventListener('click', this.boundOnClickOutside);
    }

    disconnect() {
        document.removeEventListener('click', this.boundOnClickOutside);
    }

    async loadData() {
        this.isLoading = true;
        try {
            const response = await fetch(this.prefetchUrlValue);
            this.searchResults = await response.json();
        } catch (err) {
            console.warn('Search prefetch failed:', err);
            this.searchResults = [];
        }
        this.isLoading = false;
    }

    onFocus() {
        const query = this.inputTarget.value.trim();
        if (query.length >= 1) {
            this.performSearch(query);
        }
    }

    onInput(event) {
        const query = event.target.value.trim();

        if (query.length < 1) {
            this.hideResults();
            return;
        }

        this.performSearch(query);
    }

    performSearch(query) {
        if (this.isLoading) {
            this.showLoading();
            return;
        }

        const lowerQuery = query.toLowerCase();
        const matches = this.searchResults.filter(item =>
            item.value.toLowerCase().includes(lowerQuery)
        ).slice(0, 8);

        this.showResults(matches, query);
    }

    onKeydown(event) {
        if (!this.hasResultsTarget) return;
        if (!this.resultsTarget.classList.contains('is-visible')) return;

        const items = this.resultsTarget.querySelectorAll('.search-autocomplete__item');
        if (items.length === 0) return;

        switch (event.key) {
            case 'ArrowDown':
                event.preventDefault();
                this.selectedIndex = Math.min(this.selectedIndex + 1, items.length - 1);
                this.updateSelection(items);
                break;
            case 'ArrowUp':
                event.preventDefault();
                this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
                this.updateSelection(items);
                break;
            case 'Enter':
                if (this.selectedIndex >= 0 && items[this.selectedIndex]) {
                    event.preventDefault();
                    const url = items[this.selectedIndex].dataset.url;
                    if (url) window.location.href = url;
                }
                break;
            case 'Escape':
                this.hideResults();
                this.inputTarget.blur();
                break;
        }
    }

    updateSelection(items) {
        items.forEach((item, index) => {
            item.classList.toggle('is-selected', index === this.selectedIndex);
            if (index === this.selectedIndex) {
                item.scrollIntoView({ block: 'nearest' });
            }
        });
    }

    showLoading() {
        if (!this.hasResultsTarget) return;

        this.resultsTarget.innerHTML = `
            <div class="search-autocomplete__loading">
                <div class="spinner mx-auto"></div>
            </div>
        `;
        this.resultsTarget.classList.add('is-visible');
    }

    showResults(matches, query) {
        if (!this.hasResultsTarget) return;

        this.selectedIndex = -1;

        if (matches.length === 0) {
            this.showEmpty(query);
            return;
        }

        // Group results by type
        const cities = matches.filter(m => m.type === 'city');
        const rides = matches.filter(m => m.type === 'ride');
        const content = matches.filter(m => m.type === 'content');

        let html = '';

        if (cities.length > 0) {
            html += this.renderGroup('Staedte', cities);
        }

        if (rides.length > 0) {
            html += this.renderGroup('Touren', rides);
        }

        if (content.length > 0) {
            html += this.renderGroup('Inhalte', content);
        }

        html += this.renderFooter();

        this.resultsTarget.innerHTML = html;
        this.resultsTarget.classList.add('is-visible');
    }

    renderGroup(title, items) {
        return `
            <div class="search-autocomplete__group">
                <div class="search-autocomplete__group-header">${title}</div>
                ${items.map(item => this.renderItem(item)).join('')}
            </div>
        `;
    }

    renderItem(data) {
        const iconClass = this.getIconClass(data.type);
        const icon = this.getIcon(data.type);

        let metaHtml = '';
        if (data.type === 'ride' && data.meta) {
            const metaParts = [];
            if (data.meta.dateTime) {
                const dateTime = new Date(data.meta.dateTime);
                const dateStr = dateTime.toLocaleDateString('de-DE', {
                    weekday: 'short',
                    day: 'numeric',
                    month: 'short'
                });
                const timeStr = dateTime.toLocaleTimeString('de-DE', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                metaParts.push(`<i class="far fa-calendar-alt"></i> ${dateStr}, ${timeStr}`);
            }
            if (data.meta.location) {
                metaParts.push(`<i class="far fa-map-marker-alt"></i> ${this.escapeHtml(data.meta.location)}`);
            }
            if (metaParts.length > 0) {
                metaHtml = `<div class="search-autocomplete__item-meta">${metaParts.join(' ')}</div>`;
            }
        }

        return `
            <a href="${data.url}" class="search-autocomplete__item" data-url="${data.url}">
                <div class="d-flex align-items-center">
                    <span class="search-autocomplete__item-icon ${iconClass}">
                        <i class="${icon}"></i>
                    </span>
                    <div class="search-autocomplete__item-content">
                        <div class="search-autocomplete__item-title">${this.escapeHtml(data.value)}</div>
                        ${metaHtml}
                    </div>
                </div>
            </a>
        `;
    }

    renderFooter() {
        return `
            <div class="search-autocomplete__footer">
                <span><kbd class="search-autocomplete__kbd">↑</kbd><kbd class="search-autocomplete__kbd">↓</kbd> Navigation</span>
                <span><kbd class="search-autocomplete__kbd">↵</kbd> Oeffnen</span>
                <span><kbd class="search-autocomplete__kbd">esc</kbd> Schliessen</span>
            </div>
        `;
    }

    showEmpty(query) {
        if (!this.hasResultsTarget) return;

        this.resultsTarget.innerHTML = `
            <div class="search-autocomplete__empty">
                <i class="far fa-search"></i>
                <p>Keine Ergebnisse fuer "${this.escapeHtml(query)}"</p>
            </div>
        `;
        this.resultsTarget.classList.add('is-visible');
    }

    hideResults() {
        if (this.hasResultsTarget) {
            this.resultsTarget.classList.remove('is-visible');
        }
        this.selectedIndex = -1;
    }

    onClickOutside(event) {
        if (!this.element.contains(event.target)) {
            this.hideResults();
        }
    }

    getIconClass(type) {
        const classes = {
            city: 'search-autocomplete__item-icon--city',
            ride: 'search-autocomplete__item-icon--ride',
            content: 'search-autocomplete__item-icon--content'
        };
        return classes[type] || '';
    }

    getIcon(type) {
        const icons = {
            city: 'far fa-city',
            ride: 'far fa-bicycle',
            content: 'far fa-file-alt'
        };
        return icons[type] || 'far fa-circle';
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}
