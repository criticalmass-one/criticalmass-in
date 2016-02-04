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
                        return '<ul class="typeahead dropdown-menu" role="menu">';
                    },
                    footer: function() {
                        return '</ul>';
                    },
                    suggestion: function(data) {
                        if (data.type == 'city') {
                            return '<li><a class="dropdown-item" href="' + data.url + '" role="option"><i class="fa fa-university"></i> ' + data.value + '</a></li>';
                        }

                        if (data.type == 'ride') {
                            return '<li><a class="dropdown-item" href="' + data.url + '" role="option"><i class="fa fa-bicycle"></i> ' + data.value + '</a></li>';
                        }

                        if (data.type == 'content') {
                            return '<li><a class="dropdown-item" href="' + data.url + '" role="option"><i class="fa fa-file-text-o"></i> ' + data.value + '</a></li>';
                        }

                    }
                }
            }
        );
    };

    return Search;
});
