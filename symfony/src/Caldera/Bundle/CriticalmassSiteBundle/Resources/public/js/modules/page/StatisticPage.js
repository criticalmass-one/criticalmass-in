define(['chartjs'], function() {
    StatisticPage = function(context, options) {
    };

    StatisticPage.prototype._rideMonths = [];
    StatisticPage.prototype._participantsData = [];
    StatisticPage.prototype._durationData = [];
    StatisticPage.prototype._distanceData = [];

    StatisticPage.prototype._participantsChart = null;

    StatisticPage.prototype._cities = [];

    StatisticPage.prototype.addRideData = function(citySlug, rideMonth, participants, duration, distance) {
        this._participantsData[citySlug][rideMonth] = participants;
        this._durationData[citySlug][rideMonth] = duration;
        this._distanceData[citySlug][rideMonth] = distance;
    };

    StatisticPage.prototype.addCity = function(cityName, citySlug, colorRed, colorGreen, colorBlue) {
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

    StatisticPage.prototype.addRideMonth = function (rideMonth) {
        this._rideMonths.push(rideMonth);
    };

    StatisticPage.prototype.createParticipantsChart = function($element) {
        var datasets = this._createDataset(this._participantsData);

        var data = {
            labels: this._rideMonths,
            datasets: datasets
        };

        this._participantsChart = new Chart($element, {
            type: 'line',
            data: data
        });
    };

    StatisticPage.prototype.createDurationChart = function($element) {
        var datasets = this._createDataset(this._durationData);

        var data = {
            labels: this._rideMonths,
            datasets: datasets
        };

        this._durationChart = new Chart($element, {
            type: 'line',
            data: data
        });
    };

    StatisticPage.prototype.createDistanceChart = function($element) {
        var datasets = this._createDataset(this._distanceData);

        var data = {
            labels: this._rideMonths,
            datasets: datasets
        };

        this._durationChart = new Chart($element, {
            type: 'line',
            data: data
        });
    };

    StatisticPage.prototype._createDataset = function(list) {
        var datasets = [];

        for (var citySlug in this._cities) {
            var city = this._cities[citySlug];

            var data = [];

            for (var index in this._rideMonths) {
                var rideMonth = this._rideMonths[index];

                data.push(list[citySlug][rideMonth]);
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

        return datasets;
    };

    return StatisticPage;
});