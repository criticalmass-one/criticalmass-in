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

        $('#search-input').typeahead(
            {
                hint: false,
                highlight: true,
                minLength: 1,
                classNames: {
                    dataset: 'tt-dataset tt-dataset-results container'
                }
            },
            {
                name: 'results',
                source: bh.ttAdapter(),
                displayKey: 'value',
                templates: {
                    suggestion: function(data) {
                        var html = '';
                        html += '<div class="row padding-top-small padding-bottom-small">';
                        html += '<div class="col-md-12">';

                        if (data.type == 'city') {
                            html += '<a href="' + data.url + '"><i class="fa fa-university"></i> ' + data.value + '</a>';
                        }

                        if (data.type == 'ride') {
                            html += '<a href="' + data.url + '"><i class="fa fa-bicycle"></i> ' + data.value + '</a>';
                        }

                        if (data.type == 'content') {
                            html += '<a href="' + data.url + '"><i class="fa fa-file-text-o"></i> ' + data.value + '</a>';
                        }

                        html += '</div>';
                        html += '</div>';

                        return html;
                    }
                }
            }
        );
    };

    return Search;
});