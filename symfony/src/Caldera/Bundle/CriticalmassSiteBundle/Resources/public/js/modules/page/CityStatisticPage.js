define(['chartjs'], function() {
    CityStatisticPage = function(context, options) {
    };

    CityStatisticPage.prototype._rideDates = [];
    CityStatisticPage.prototype._participantsData = [];
    CityStatisticPage.prototype._durationData = [];
    CityStatisticPage.prototype._distanceData = [];

    CityStatisticPage.prototype._participantsChart = null;

    CityStatisticPage.prototype._colorRed = 0;
    CityStatisticPage.prototype._colorGreen = 0;
    CityStatisticPage.prototype._colorBlue = 0;

    CityStatisticPage.prototype.addRideData = function(rideDate, participants, duration, distance) {
        this._rideDates.push(rideDate);
        this._participantsData.push(participants);
        this._durationData.push(duration);
        this._distanceData.push(distance);
    };

    CityStatisticPage.prototype.setColor = function(colorRed, colorGreen, colorBlue) {
        this._colorRed = colorRed;
        this._colorGreen = colorGreen;
        this._colorBlue = colorBlue;
    };

    CityStatisticPage.prototype.createParticipantsChart = function($element) {
        var colorString = 'rgba(' + this._colorRed + ', ' + this._colorGreen + ', ' + this._colorBlue + ', 1)';

        var datasets = [];

        datasets.push(this._createDataset('Teilnehmer', colorString, this._participantsData));

        var data = {
            labels: this._rideDates,
            datasets: datasets
        };

        this._participantsChart = new Chart($element, {
            type: 'line',
            data: data
        });
    };

    CityStatisticPage.prototype.createDistanceChart = function($element) {
        var colorString = 'rgba(' + this._colorRed + ', ' + this._colorGreen + ', ' + this._colorBlue + ', 1)';

        var datasets = [];

        datasets.push(this._createDataset('Fahrtlänge', colorString, this._distanceData));

        var data = {
            labels: this._rideDates,
            datasets: datasets
        };

        this._participantsChart = new Chart($element, {
            type: 'line',
            data: data
        });
    };

    CityStatisticPage.prototype.createDurationChart = function($element) {
        var colorString = 'rgba(' + this._colorRed + ', ' + this._colorGreen + ', ' + this._colorBlue + ', 1)';

        var datasets = [];

        datasets.push(this._createDataset('Fahrtdauer', colorString, this._durationData));

        var data = {
            labels: this._rideDates,
            datasets: datasets
        };

        this._participantsChart = new Chart($element, {
            type: 'line',
            data: data
        });
    };

    CityStatisticPage.prototype._createDataset = function(label, colorString, data) {
        return {
            label: label,
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
            yAxisID: "y-axis-0"
        }
    };

    return CityStatisticPage;
});