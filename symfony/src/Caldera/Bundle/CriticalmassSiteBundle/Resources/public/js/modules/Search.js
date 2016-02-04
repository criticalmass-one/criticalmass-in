define(['typeahead', 'bloodhound', 'dateformat'], function() {

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
                            html += '<a href="' + data.url + '">';
                            html += '<div class="row">';
                            html += '<div class="col-md-12">';
                            html += '<i class="fa fa-bicycle"></i> ';
                            html += data.value;
                            html += '</div>';
                            html += '</div>';

                            if (data.meta.location.length > 0) {
                                html += '<div class="row">';
                                html += '<div class="col-md-12">';
                                html += data.meta.location;
                                html += '</div>';
                                html += '</div>';
                            }

                            var dateTime = new Date(data.meta.dateTime);

                            html += '<div class="row">';
                            html += '<div class="col-md-12">';
                            html += dateTime.format('dd.mm.yyyy HH:MM') + '&nbsp;Uhr';
                            html += '</div>';
                            html += '</div>';
                        }

                        if (data.type == 'content') {
                            html += '<a href="' + data.url + '"><i class="fa fa-file-text-o"></i> ' + data.value + '</a>';
                        }

                        html += '</div>';
                        html += '</div>';
                        html += '</a>';

                        return html;
                    }
                }
            }
        );
    };

    return Search;
});