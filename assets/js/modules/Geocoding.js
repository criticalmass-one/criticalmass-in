define([], function () {

    Geocoding = function (context, options) {
    };

    Geocoding.prototype._country = null;
    Geocoding.prototype._state = null;

    Geocoding.prototype.setCountry = function (country) {
        this._country = country;
    };

    Geocoding.prototype.setState = function (state) {
        this._state = state;
    };

    Geocoding.prototype._query = function (query, successCallback) {
        var baseUrl = 'https://nominatim.openstreetmap.org/search?';

        var defaultOptions = {
            format: 'json'
        };

        var jsonQuery = $.extend(query, defaultOptions);
        var params = jQuery.param(jsonQuery);

        var url = baseUrl + params;

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: url,
            cache: false,
            success: successCallback
        });
    };

    Geocoding.prototype._reverseQuery = function (query, successCallback) {
        var baseUrl = 'https://nominatim.openstreetmap.org/reverse?';

        var defaultOptions = {
            format: 'json'
        };

        var jsonQuery = $.extend(query, defaultOptions);
        var params = jQuery.param(jsonQuery);

        var url = baseUrl + params;

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: url,
            cache: false,
            success: successCallback
        });
    };

    Geocoding.prototype.searchState = function (stateName, returnCallback) {
        var query = {
            state: stateName,
            country: this._country
        };

        var successCallback = function (data) {
            var importanceScore = 0.0;
            var bestData = null;

            for (var index in data) {
                if (importanceScore < data[index].importance || !bestData) {
                    importanceScore = data[index].importance;
                    bestData = data[index];
                }
            }

            returnCallback(bestData);
        };

        this._query(query, successCallback);
    };

    Geocoding.prototype.searchCity = function (cityName, returnCallback) {
        var query = {
            city: cityName,
            state: this._state
        };

        var successCallback = function (data) {
            var importanceScore = 0.0;
            var bestData = null;

            for (var index in data) {
                if (importanceScore < data[index].importance || !bestData) {
                    importanceScore = data[index].importance;
                    bestData = data[index];
                }
            }

            returnCallback(bestData);
        };

        this._query(query, successCallback);
    };

    Geocoding.prototype.searchCountry = function (countryName, returnCallback) {
        var query = {
            country: countryName
        };

        var successCallback = function (data) {
            var importanceScore = 0.0;
            var bestData = null;

            for (var index in data) {
                if (importanceScore < data[index].importance || !bestData) {
                    importanceScore = data[index].importance;
                    bestData = data[index];
                }
            }

            returnCallback(bestData);
        };

        this._query(query, successCallback);
    };

    Geocoding.prototype.searchPlace = function (placeName, cityName, returnCallback) {
        var query = {
            q: placeName,
            city: cityName
        };

        var successCallback = function (data) {
            var importanceScore = 0.0;
            var bestData = null;

            for (var index in data) {
                if (importanceScore < data[index].importance || !bestData) {
                    importanceScore = data[index].importance;
                    bestData = data[index];
                }
            }

            returnCallback(bestData);
        };

        this._query(query, successCallback);
    };

    Geocoding.prototype.searchPhrase = function (phrase, returnCallback) {
        var query = {
            q: phrase
        };

        var successCallback = function (data) {
            var importanceScore = 0.0;
            var bestData = null;

            for (var index in data) {
                if (importanceScore < data[index].importance || !bestData) {
                    importanceScore = data[index].importance;
                    bestData = data[index];
                }
            }

            returnCallback(bestData);
        };

        this._query(query, successCallback);
    };

    Geocoding.prototype.searchAddressForLatLng = function (latitude, longitude, returnCallback) {
        var query = {
            lat: latitude,
            lon: longitude
        };

        var successCallback = function (data) {
            returnCallback(data.address);
        };

        this._reverseQuery(query, successCallback);
    };

    Geocoding.prototype.searchNameForLatLng = function (latitude, longitude, returnCallback) {
        var query = {
            lat: latitude,
            lon: longitude
        };

        var successCallback = function (data) {
            returnCallback(data.display_name);
        };

        this._reverseQuery(query, successCallback);
    };

    Geocoding.prototype.searchPhraseInViewbox = function (phrase, north, south, west, east, returnCallback) {
        var query = {
            q: phrase,
            viewbox: west + ',' + north + ',' + east + ',' + south,
        };

        var successCallback = function (data) {
            var importanceScore = 0.0;
            var bestData = null;

            for (var index in data) {
                if (importanceScore < data[index].importance || !bestData) {
                    importanceScore = data[index].importance;
                    bestData = data[index];
                }
            }

            returnCallback(bestData);
        };

        this._query(query, successCallback);
    };

    return Geocoding;
});