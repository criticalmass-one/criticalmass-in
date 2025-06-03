import Typeahead from '../../node_modules/typeahead.js/dist/typeahead.jquery';
import Bloodhound from '../../node_modules/typeahead.js/dist/bloodhound';
import dateFormat from '../../node_modules/dateformat/lib/dateformat';

export default class Search {
    bloodhound;
    typeahead;
    
    constructor(context, options) {
        this._$input = $(context);

        this.initBloodhound();
        this.initTypeahead();
    }

    initBloodhound() {
        const url = Routing.generate('caldera_criticalmass_search_prefetch');

        this.bloodhound = new Bloodhound({
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

        this.bloodhound.initialize();
    }

    initTypeahead() {
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
                source: this.bloodhound.ttAdapter(),
                displayKey: 'value',
                templates: {
                    suggestion: this.templateSuggestionFunction.bind(this)
                }
            }
        );
    }

    renderCitySuggestion(data) {
        return '<a href="' + data.url + '"><i class="far fa-university"></i> ' + data.value + '</a>';
    }

    renderRideSuggestion(data) {
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
    }

    renderContentSuggestion(data) {
        return '<a href="' + data.url + '"><i class="far fa-file-text"></i> ' + data.value + '</a>';
    }

    templateSuggestionFunction(data) {
        var html = '';
        html += '<div class="row padding-top-small padding-bottom-small">';
        html += '<div class="col-md-12">';

        if (data.type == 'city') {
            html += this.renderCitySuggestion(data);
        }

        if (data.type == 'ride') {
            html += this.renderRideSuggestion(data);
        }

        if (data.type == 'content') {
            html += this.renderContentSuggestion(data);
        }

        html += '</div>';
        html += '</div>';

        return html;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');

    if (searchInput) {
        new Search(searchInput);
    }
});