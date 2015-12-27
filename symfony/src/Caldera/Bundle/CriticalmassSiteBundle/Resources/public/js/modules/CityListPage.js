define([], function() {
        var CityListPage = function(context, settings) {
            this.context = context;
            this.settings = settings;

            this._init();
        };

        CityListPage.prototype._sortByCity = function() {
            _paq.push(['trackEvent', 'sortCityList', 'City']);

            var sortFunction = function(a, b)
            {
                return ($(a).data('cityname') > $(b).data('cityname') ? 1 : -1);
            };

            this._sortCityList(sortFunction);
        };

        CityListPage.prototype._sortByDistance = function() {
            _paq.push(['trackEvent', 'sortCityList', 'Distance']);

            var that = this;

            function successCallback(geolocationResult)
            {
                var latitude = geolocationResult.coords.latitude;
                var longitude = geolocationResult.coords.longitude;

                var sortFunction = function(a, b)
                {
                    var distanceA = Math.sqrt(Math.pow(latitude - $(a).data('latitude'), 2) + Math.pow(longitude - $(a).data('longitude'), 2));
                    var distanceB = Math.sqrt(Math.pow(latitude - $(b).data('latitude'), 2) + Math.pow(longitude - $(b).data('longitude'), 2));

                    return distanceA - distanceB;
                };

                that._sortCityList(sortFunction);
            }

            navigator.geolocation.getCurrentPosition(successCallback);
        };

        CityListPage.prototype._sortByNextRide = function() {
            _paq.push(['trackEvent', 'sortCityList', 'Ride']);

            var sortFunction = function(a, b)
            {
                return ($(a).data('currentride') > $(b).data('currentride') ? 1 : -1);
            };

            this._sortCityList(sortFunction);
        };

        CityListPage.prototype._init = function() {
            var that = this;

            $('#sortByCity').on('click', function() {
                that._sortByCity();
            });

            $('#sortByDistance').on('click', function() {
                that._sortByDistance();
            });

            $('#sortByNextRide').on('click', function() {
                that._sortByNextRide();
            });
        };

        CityListPage.prototype._sortCityList = function(sortFunction) {
            var tableHeaderRow = $('#tableHeaderRow').html();
            $('#tableHeaderRow').remove();

            $('#cityList tr').sort(sortFunction).map(function () {
                return $(this).closest('#cityList tr');
            }).each(function (_, container) {
                $(container).parent().append(container);
            });

            $('#cityList').prepend('<tr id="tableHeaderRow">' + tableHeaderRow + '</tr>');
        };

        return CityListPage;
    }
);