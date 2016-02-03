define(['typeahead', 'bloodhound'], function() {

    Search = function(context, options) {
        var bh = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch: {
                url: '/app_dev.php/search/prefetch',
                cache: true,
                ttl: 3600
            }
        });

        bh.initialize();

        $('#search-input').typeahead(
            {
                items: 8,
                source: bh.ttAdapter()
            });
    };

    return Search;
});
