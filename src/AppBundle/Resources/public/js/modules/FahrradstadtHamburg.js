define(['jquery', 'localforage'], function ($, localforage) {
    FahrradstadtHamburg = function (context, options) {
        this._$container = $(context);

        this._initStorage();

        this._init();
    };

    FahrradstadtHamburg.prototype._$container = null;

    FahrradstadtHamburg.prototype._initStorage = function() {
        localforage.config({
            driver      : localforage.LOCALSTORAGE,
            name        : 'criticalmass',
            storeName   : 'fahrradstadt-hamburg'
        });
    };

    FahrradstadtHamburg.prototype._init = function() {
        var that = this;

        localforage.length().then(function(rowCounter) {
            if (rowCounter > 1) {
                localforage.getItem('last-update').then(function(lastUpdate) {
                    var diff = 1800 * 1000;

                    if (lastUpdate < (Date.now() - diff)) {
                        localforage.clear().then(function() {
                            that._fetchFeed();

                            console.log('Fahrradstadt: Refreshing feed');
                        });
                    } else {
                        that._displayRandomEntry();

                        console.log('Fahrradstadt: Show entry from cache');
                    }
                });
            } else {
                that._fetchFeed();

                console.log('Fahrradstadt: Fetching feed')
            }
        });
    };

    FahrradstadtHamburg.prototype._fetchFeed = function() {
        $.ajax({
            url: 'https://fahrradstadt.hamburg/feed',
            dataType: 'xml',
            context: this,
            success: function (result) {
                var $feed = $(result);
                var itemList = $feed.find('item');

                for (var i = 0; i < itemList.length; i++) {
                    var $item = $(itemList[i]);

                    var itemObject = {
                        url: $item.find('link').text(),
                        title: $item.find('title').text(),
                        imageUrl: $item.find('enclosure').attr('url')
                    };

                    localforage.setItem('fahrradstadt-feed-item-' + i, itemObject);
                }

                localforage.setItem('last-update', Date.now());

                this._displayRandomEntry();
            }
        });
    };

    FahrradstadtHamburg.prototype._displayRandomEntry = function() {
        var that = this;

        localforage.keys().then(function(keys) {
            var randomKey = keys[Math.floor(Math.random() * keys.length)];

            localforage.getItem(randomKey).then(function(item) {
                $panel = $('<div class="panel panel-fahrradstadt"><div class="panel-heading"><h3 class="panel-title">Fahrradstadt.Hamburg</h3></div><div class="panel-body"><div class="row"><div class="col-md-12"><a href="' + item.url + '"><img class="img-responsive" src="' + item.imageUrl + '" /></a></div></div><div class="row"><div class="col-md-12 text-center"><a href="' + item.url + '"><h3 class="h4">' + item.title +'</h3></a></div></div><div class="row"><div class="col-md-12 text-center"><a class="btn btn-fahrradstadt" href="' + item.url + '">zur Fahrradstadt</a></div></div></div></div>');

                that._$container.append($panel);
            });
        });
    };

    return FahrradstadtHamburg;
});
