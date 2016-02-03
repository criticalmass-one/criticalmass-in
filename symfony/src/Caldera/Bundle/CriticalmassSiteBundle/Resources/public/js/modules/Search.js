define(['typeahead', 'bloodhound'], function() {

    Search = function(context, options) {
        var numbers = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            local:  ["(A)labama","Alaska","Arizona","Arkansas","Arkansas2","Barkansas"]
        });

// initialize the bloodhound suggestion engine
        numbers.initialize();

        $('#search-input').typeahead(
            {
                items: 4,
                source:numbers.ttAdapter()
            });
    };

    return Search;
});
