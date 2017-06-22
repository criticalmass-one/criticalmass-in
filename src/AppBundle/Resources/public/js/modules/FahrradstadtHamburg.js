define(['jquery'], function ($) {
    FahrradstadtHamburg = function (context, options) {
        this._$container = $(context);

        this._init();
    };

    FahrradstadtHamburg.prototype._$container = null;

    FahrradstadtHamburg.prototype._init = function() {
        $.ajax({
            url: 'https://fahrradstadt.hamburg/feed',
            dataType: 'xml',
            context: this,
            success: function (result) {
                var $feed = $(result);
                var itemList = $feed.find('item');

                var $item = $(itemList[Math.floor(Math.random() * itemList.length)]);

                var url = $item.find('link').text();
                var title = $item.find('title').text();
                var imageUrl = $item.find('enclosure').attr('url');
                imageUrl = 'https://fahrradstadt.hamburg/media/cache/standard/photos/594b722e4a510.JPG';

                $panel = $('<div class="panel panel-default"><div class="panel-heading">Fahrradstadt.Hamburg</div><div class="panel-body"><div class="row"><div class="col-md-12"><a href="' + url + '"><img class="img-responsive" src="' + imageUrl + '" /></a></div></div><div class="row"><div class="col-md-12 text-center"><a href="' + url + '"><h3 class="h4">' + title +'</h3></a></div></div><div class="row"><div class="col-md-12 text-center"><a href="' + url + '">zur Fahrradstadt</a></div></div></div></div>');

                this._$container.append($panel);

            }
        });
    };

    return FahrradstadtHamburg;
});
