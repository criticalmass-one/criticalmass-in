define([], function () {

    ScrollToPost = function (context, options) {

        let hash = this._getHash(window.location.hash);

        if (hash.startsWith('post-')) {
            this._highlightPost(hash);
        }

        this._addEventListers();
    };

    ScrollToPost.prototype._getHash = function (url) {
        let idx = url.indexOf('#');
        let hash = idx != -1 ? url.substring(idx+1) : '';

        return hash;
    };

    ScrollToPost.prototype._addEventListers = function () {
        let that = this;

        $('a[href^=\'#post-\']').on('click', function (e) {
            e.preventDefault();

            let hash = that._getHash($(this).attr('href'));

            location.hash = hash;

            that._unhighlightPosts();
            that._highlightPost(hash);
        });
    };

    ScrollToPost.prototype._highlightPost = function (hash) {
        let $anchor = $('a[name=' + hash + ']');
        let $post = $anchor.closest('.post');
        let $tabContent = $post.closest('.tab-pane');
        let $tab = $('a[href=\'#' + $tabContent.attr('id') + '\']');

        $post.addClass('bg-warning');

        $tab.tab('show');
    };

    ScrollToPost.prototype._unhighlightPosts = function () {
         $('.post').removeClass('bg-warning');
    };

    return ScrollToPost;
});
