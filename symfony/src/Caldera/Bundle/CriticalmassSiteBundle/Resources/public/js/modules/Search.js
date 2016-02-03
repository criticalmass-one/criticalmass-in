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

        $('#search-input').typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            },
            {
                name: 'results',
                source: bh.ttAdapter(),
                templates: {

                }
            }
        );
    };

    return Search;
});
