define([], function () {

    ScrollToPost = function (context, options) {

        var url = window.location.hash;
        var idx = url.indexOf('#');
        var hash = idx != -1 ? url.substring(idx+1) : '';

        if (hash.startsWith('post-')) {
            var $anchor = $('a[name=' + hash + ']');
            var $post = $anchor.closest('.post');
            var $tabContent = $post.closest('.tab-pane');
            var $tab = $('a[href=\'#' + $tabContent.attr('id') + '\']');

            $post.addClass('bg-warning');

            $tab.tab('show');
        }
    };

    return ScrollToPost;
});
