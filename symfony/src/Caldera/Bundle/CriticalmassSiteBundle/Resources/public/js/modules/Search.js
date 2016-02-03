define(['typeahead', 'bloodhound'], function() {

    Search = function(context, options) {
        var bh = new Bloodhound({
            datumTokenizer: function(data) {
                return Bloodhound.tokenizers.whitespace(data.value);
            },
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
                    header: function() {
                        return '<ul class="typeahead dropdown-menu" role="listbox">';
                    },
                    footer: function() {
                        return '</ul>';
                    },
                    suggestion: function(data) {
                        return '<li><a class="dropdown-item" href="' + data.url + '" role="option">' + data.value + '</a></li>';
                    }
                }
            }
        );
    };

    return Search;
});
