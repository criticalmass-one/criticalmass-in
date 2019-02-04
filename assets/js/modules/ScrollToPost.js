define([], function () {

    ScrollToPost = function (context, options) {

        var hash = this._getHash(window.location.hash);

        if (hash.startsWith('post-')) {
            this._highlightPost(hash);
        }

        this._addEventListers();
    };

    ScrollToPost.prototype._getHash = function (url) {
        var idx = url.indexOf('#');
        var hash = idx != -1 ? url.substring(idx+1) : '';

        return hash;
    };

    ScrollToPost.prototype._addEventListers = function () {
        $('a[href^=\'#post-\']').on('click', function (e) {
            e.preventDefault();

            alert($(e.target).attr('href'));
            var hash = this._getHash($(this).attr('href'));

            location.hash = hash;

            this._unhighlightPosts();
            this._highlightPost(this._getHash());
        }.bind(this));
    };

    ScrollToPost.prototype._highlightPost = function (hash) {
        var $anchor = $('a[name=' + hash + ']');
        var $post = $anchor.closest('.post');
        var $tabContent = $post.closest('.tab-pane');
        var $tab = $('a[href=\'#' + $tabContent.attr('id') + '\']');

        $post.addClass('bg-warning');

        $tab.tab('show');
    };

    ScrollToPost.prototype._unhighlightPosts = function () {
         $('.post').removeClass('bg-warning');
    };

    return ScrollToPost;
});
