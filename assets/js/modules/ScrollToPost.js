define([], function () {

    ScrollToPost = function (context, options) {

        var hash = this._getHash();

        if (hash.startsWith('post-')) {
            this._highlightPost(hash);
        }

        this._addEventListers();
    };

    ScrollToPost.prototype._getHash = function () {
        var url = window.location.hash;
        var idx = url.indexOf('#');
        var hash = idx != -1 ? url.substring(idx+1) : '';

        return hash;
    };

    ScrollToPost.prototype._addEventListers = function () {
        $('a[href^=\'#post-\']').on('click', function (e) {
            e.preventDefault();

            this._highlightPost(this._getHash());
        });
    };

    ScrollToPost.prototype._highlightPost = function (hash) {
        var $anchor = $('a[name=' + hash + ']');
        var $post = $anchor.closest('.post');
        var $tabContent = $post.closest('.tab-pane');
        var $tab = $('a[href=\'#' + $tabContent.attr('id') + '\']');

        $post.addClass('bg-warning');

        $tab.tab('show');
    };

    return ScrollToPost;
});
