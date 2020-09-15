define(['dateformat', 'typeahead.jquery', 'bloodhound'], function (dateFormat) {

    Search = function (context, options) {
        this._$input = $(context);

        this._initBloodhound();
        this._initTypeahead();
    };

    Search.prototype._bloodhound = null;

    Search.prototype._initBloodhound = function () {
        var url = Routing.generate('caldera_criticalmass_search_prefetch');

        this._bloodhound = new Bloodhound({
            datumTokenizer: function (data) {
                return Bloodhound.tokenizers.whitespace(data.value);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch: {
                url: url,
                cache: true,
                ttl: 3600
            }
        });

        this._bloodhound.initialize();
    };

    Search.prototype._initTypeahead = function () {
        $(this._$input).typeahead(
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
                source: this._bloodhound.ttAdapter(),
                displayKey: 'value',
                templates: {
                    suggestion: this._templateSuggestionFunction.bind(this)
                }
            }
        );
    };

    Search.prototype._renderCitySuggestion = function (data) {
        return '<a href="' + data.url + '"><i class="far fa-university"></i> ' + data.value + '</a>';
    };

    Search.prototype._renderRideSuggestion = function (data) {
        var html = '';

        html += '<a href="' + data.url + '">';
        html += '<div class="row">';
        html += '<div class="col-md-12">';
        html += '<i class="far fa-bicycle"></i> ';
        html += data.value;
        html += '</div>';
        html += '</div>';

        if (data.meta && data.meta.location && data.meta.location.length > 0) {
            html += '<div class="row">';
            html += '<div class="col-md-12">';
            html += data.meta.location;
            html += '</div>';
            html += '</div>';
        }

        var dateTime = new Date(data.meta.dateTime);

        html += '<div class="row">';
        html += '<div class="col-md-12">';
        html += dateFormat(dateTime, 'dd.mm.yyyy HH:MM') + '&nbsp;Uhr';
        html += '</div>';
        html += '</div>';
        html += '</a>';

        return html;
    };

    Search.prototype._renderContentSuggestion = function (data) {
        return '<a href="' + data.url + '"><i class="far fa-file-text"></i> ' + data.value + '</a>';
    };

    Search.prototype._templateSuggestionFunction = function (data) {
        var html = '';
        html += '<div class="row padding-top-small padding-bottom-small">';
        html += '<div class="col-md-12">';

        if (data.type == 'city') {
            html += this._renderCitySuggestion(data);
        }

        if (data.type == 'ride') {
            html += this._renderRideSuggestion(data);
        }

        if (data.type == 'content') {
            html += this._renderContentSuggestion(data);
        }

        html += '</div>';
        html += '</div>';

        return html;
    };

    return Search;
});
