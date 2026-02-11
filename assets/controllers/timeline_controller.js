import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        apiUrl: String,
        year: Number,
        month: Number,
        showNavigation: { type: Boolean, default: true },
    };

    static targets = ['tabs', 'tabContent', 'navigation', 'loading'];

    tabConfig = [
        { key: 'standard', icon: 'far fa-clock', label: 'Neuigkeiten' },
        { key: 'facebook', icon: 'fab fa-facebook', label: 'Facebook-Beiträge' },
        { key: 'twitter', icon: 'fab fa-twitter', label: 'Tweets' },
        { key: 'instagram_profile', icon: 'fab fa-instagram', label: 'Instagram-Fotos' },
        { key: 'homepage', icon: 'far fa-globe', label: 'Blog-Beiträge' },
        { key: 'mastodon', icon: 'fab fa-mastodon', label: 'Mastodon-Beiträge' },
    ];

    connect() {
        this.loadTimeline();
    }

    async loadTimeline() {
        this.showLoading();

        try {
            const url = this.apiUrlValue
                .replace('{year}', String(this.yearValue).padStart(4, '0'))
                .replace('{month}', String(this.monthValue).padStart(2, '0'));

            const response = await fetch(url);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();

            this.renderTabs(data.tabs);

            if (this.showNavigationValue) {
                this.renderNavigation(data.navigation);
            }
        } catch (error) {
            this.renderError();
        }
    }

    renderTabs(tabs) {
        const availableTabs = this.tabConfig.filter(tab => tabs[tab.key] && tabs[tab.key].length > 0);

        if (availableTabs.length === 0) {
            this.hideLoading();
            this.tabsTarget.innerHTML = '';
            this.tabContentTarget.innerHTML = '<p class="text-muted">Keine Einträge für diesen Zeitraum.</p>';
            return;
        }

        let tabsHtml = '<ul class="nav nav-tabs" role="tablist">';

        availableTabs.forEach((tab, index) => {
            const isActive = index === 0;
            tabsHtml += `
                <li class="nav-item" role="presentation">
                    <button class="nav-link${isActive ? ' active' : ''}"
                            id="${tab.key}-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#${tab.key}"
                            type="button"
                            role="tab"
                            aria-controls="${tab.key}"
                            aria-selected="${isActive}">
                        <i class="${tab.icon}"></i>
                        ${tab.label}
                    </button>
                </li>`;
        });

        tabsHtml += '</ul>';
        this.tabsTarget.innerHTML = tabsHtml;

        let contentHtml = '';

        availableTabs.forEach((tab, index) => {
            const isActive = index === 0;
            contentHtml += `
                <div role="tabpanel"
                     class="tab-pane fade${isActive ? ' show active' : ''}"
                     id="${tab.key}"
                     aria-labelledby="${tab.key}-tab">
                    ${tabs[tab.key].join('')}
                </div>`;
        });

        this.tabContentTarget.innerHTML = contentHtml;
        this.hideLoading();

        this.tabsTarget.addEventListener('shown.bs.tab', () => {
            this.element.querySelectorAll('.leaflet-container').forEach(map => {
                map._leaflet_map?.invalidateSize();
            });
        });
    }

    renderNavigation(navigation) {
        if (!this.hasNavigationTarget) {
            return;
        }

        const prevDisabled = !navigation.previous;
        const nextDisabled = !navigation.next;

        const prevHref = navigation.previous
            ? `/timeline/${String(navigation.previous.year).padStart(4, '0')}/${String(navigation.previous.month).padStart(2, '0')}`
            : '#';
        const nextHref = navigation.next
            ? `/timeline/${String(navigation.next.year).padStart(4, '0')}/${String(navigation.next.month).padStart(2, '0')}`
            : '#';

        this.navigationTarget.innerHTML = `
            <nav aria-label="Monat Navigation">
                <ul class="pagination justify-content-between">
                    <li class="page-item${prevDisabled ? ' disabled' : ''}">
                        <a class="page-link" href="${prevHref}">&larr; Voriger Monat</a>
                    </li>
                    <li class="page-item${nextDisabled ? ' disabled' : ''}">
                        <a class="page-link" href="${nextHref}">Nächster Monat &rarr;</a>
                    </li>
                </ul>
            </nav>`;
    }

    renderError() {
        this.hideLoading();
        this.tabsTarget.innerHTML = '';
        this.tabContentTarget.innerHTML = `
            <div class="alert alert-danger" role="alert">
                Die Timeline konnte leider nicht geladen werden. Bitte versuche es später erneut.
            </div>`;
    }

    showLoading() {
        if (this.hasLoadingTarget) {
            this.loadingTarget.classList.remove('d-none');
        }
        this.tabsTarget.innerHTML = '';
        this.tabContentTarget.innerHTML = '';
        if (this.hasNavigationTarget) {
            this.navigationTarget.innerHTML = '';
        }
    }

    hideLoading() {
        if (this.hasLoadingTarget) {
            this.loadingTarget.classList.add('d-none');
        }
    }
}
