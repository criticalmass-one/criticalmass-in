import { Controller } from '@hotwired/stimulus';
import { Tab } from 'bootstrap';

export default class extends Controller {
    connect() {
        const hash = this.getHash(window.location.hash);

        if (hash.startsWith('post-')) {
            this.highlightPost(hash);
        }
    }

    navigate(event) {
        event.preventDefault();

        const hash = this.getHash(event.currentTarget.getAttribute('href'));

        window.location.hash = hash;

        this.unhighlightPosts();
        this.highlightPost(hash);
    }

    getHash(url) {
        const idx = url.indexOf('#');
        return idx !== -1 ? url.substring(idx + 1) : '';
    }

    highlightPost(hash) {
        const anchor = document.querySelector(`a[name="${hash}"]`);
        if (!anchor) return;

        const post = anchor.closest('.post');
        if (!post) return;

        post.classList.add('bg-warning');

        const tabContent = post.closest('.tab-pane');
        if (tabContent) {
            const tabLink = document.querySelector(`a[href="#${tabContent.id}"], button[data-bs-target="#${tabContent.id}"]`);
            if (tabLink) {
                const tab = new Tab(tabLink);
                tab.show();
            }
        }

        post.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    unhighlightPosts() {
        document.querySelectorAll('.post.bg-warning').forEach(post => {
            post.classList.remove('bg-warning');
        });
    }
}
