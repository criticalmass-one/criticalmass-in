export default class ScrollToPost {
    constructor() {
        let hash = this.getHash(window.location.hash);

        if (hash.startsWith('post-')) {
            this.highlightPost(hash);
        }

        this.addEventListers();
    }

    getHash(url) {
        let idx = url.indexOf('#');
        let hash = idx != -1 ? url.substring(idx+1) : '';

        return hash;
    }

    addEventListers() {
        let that = this;

        $('a[href^=\'#post-\']').on('click', (e) => {
            e.preventDefault();

            let hash = that.getHash($(this).attr('href'));

            location.hash = hash;

            that.unhighlightPosts();
            that.highlightPost(hash);
        });
    }

    highlightPost(hash) {
        let $anchor = $('a[name=' + hash + ']');
        let $post = $anchor.closest('.post');
        let $tabContent = $post.closest('.tab-pane');
        let $tab = $('a[href=\'#' + $tabContent.attr('id') + '\']');

        $post.addClass('bg-warning');

        $tab.tab('show');
    }

    unhighlightPosts() {
        $('.post').removeClass('bg-warning');
    }
}

document.addEventListener('DOMContentLoaded', () => {
     new ScrollToPost();
});
