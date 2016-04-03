define(['chartjs'], function() {
    CityStatisticPage = function(context, options) {
    };

    CityStatisticPage.prototype._rideMonths = [];
    CityStatisticPage.prototype._participantsData = [];
    CityStatisticPage.prototype._durationData = [];
    CityStatisticPage.prototype._distanceData = [];

    CityStatisticPage.prototype._participantsChart = null;

    CityStatisticPage.prototype._cities = [];

    CityStatisticPage.prototype.addRideData = function(citySlug, rideMonth, participants, duration, distance) {
        this._participantsData[citySlug][rideMonth] = participants;
        this._durationData[citySlug][rideMonth] = duration;
        this._distanceData[citySlug][rideMonth] = distance;
    };

    CityStatisticPage.prototype.addCity = function(cityName, citySlug, colorRed, colorGreen, colorBlue) {
        this._participantsData[citySlug] = [];
        this._durationData[citySlug] = [];
        this._distanceData[citySlug] = [];

        this._cities[citySlug] = [
            cityName,
            colorRed,
            colorGreen,
            colorBlue
        ];
    };

    CityStatisticPage.prototype.addRideMonth = function (rideMonth) {
        this._rideMonths.push(rideMonth);
    };

    CityStatisticPage.prototype.createParticipantsChart = function($element) {
        var datasets = [];

        for (var citySlug in this._cities) {
            var city = this._cities[citySlug];

            var data = [];

            for (var index in this._rideMonths) {
                var rideMonth = this._rideMonths[index];

                data.push(this._participantsData[citySlug][rideMonth]);
            }

            var colorString = 'rgba(' + city[1] + ', ' + city[2] + ', ' + city[3] + ',1)';

            datasets.push({
                label: city[0],
                fill: false,
                backgroundColor: colorString,
                borderColor: colorString,
                borderCapStyle: 'butt',
                borderDash: [],
                borderDashOffset: 0.0,
                borderJoinStyle: 'miter',
                pointBorderColor: colorString,
                pointBackgroundColor: "#fff",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: colorString,
                pointHoverBorderColor: colorString,
                pointHoverBorderWidth: 2,
                tension: 0.1,
                data: data,
                yAxisID: "y-axis-0",
            });
        }

        var data = {
            labels: this._rideMonths,
            datasets: datasets
        };

        this._participantsChart = new Chart($element, {
            type: 'line',
            data: data
        });
    };

    CityStatisticPage.prototype.createDurationChart = function($element) {
        var datasets = [];

        for (var citySlug in this._cities) {
            var city = this._cities[citySlug];

            var data = [];

            for (var index in this._rideMonths) {
                var rideMonth = this._rideMonths[index];

                data.push(this._durationData[citySlug][rideMonth]);
            }

            var colorString = 'rgba(' + city[1] + ', ' + city[2] + ', ' + city[3] + ',1)';

            datasets.push({
                label: city[0],
                fill: false,
                backgroundColor: colorString,
                borderColor: colorString,
                borderCapStyle: 'butt',
                borderDash: [],
                borderDashOffset: 0.0,
                borderJoinStyle: 'miter',
                pointBorderColor: colorString,
                pointBackgroundColor: "#fff",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: colorString,
                pointHoverBorderColor: colorString,
                pointHoverBorderWidth: 2,
                tension: 0.1,
                data: data,
                yAxisID: "y-axis-0",
            });
        }

        var data = {
            labels: this._rideMonths,
            datasets: datasets
        };

        this._durationChart = new Chart($element, {
            type: 'line',
            data: data
        });
    };

    CityStatisticPage.prototype.createDistanceChart = function($element) {
        var datasets = [];

        for (var citySlug in this._cities) {
            var city = this._cities[citySlug];

            var data = [];

            for (var index in this._rideMonths) {
                var rideMonth = this._rideMonths[index];

                data.push(this._durationData[citySlug][rideMonth]);
            }

            var colorString = 'rgba(' + city[1] + ', ' + city[2] + ', ' + city[3] + ',1)';

            datasets.push({
                label: city[0],
                fill: false,
                backgroundColor: colorString,
                borderColor: colorString,
                borderCapStyle: 'butt',
                borderDash: [],
                borderDashOffset: 0.0,
                borderJoinStyle: 'miter',
                pointBorderColor: colorString,
                pointBackgroundColor: "#fff",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: colorString,
                pointHoverBorderColor: colorString,
                pointHoverBorderWidth: 2,
                tension: 0.1,
                data: data,
                yAxisID: "y-axis-0",
            });
        }

        var data = {
            labels: this._rideMonths,
            datasets: datasets
        };

        this._durationChart = new Chart($element, {
            type: 'line',
            data: data
        });
    };

    return CityStatisticPage;
});