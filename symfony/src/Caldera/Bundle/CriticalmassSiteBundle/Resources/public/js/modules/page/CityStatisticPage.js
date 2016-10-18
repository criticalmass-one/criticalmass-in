define(['chartjs'], function () {
    CityStatisticPage = function (context, options) {
    };

    CityStatisticPage.prototype._rideDates = [];
    CityStatisticPage.prototype._participantsData = [];
    CityStatisticPage.prototype._durationData = [];
    CityStatisticPage.prototype._distanceData = [];

    CityStatisticPage.prototype.addRideData = function (rideDate, participants, duration, distance) {
        this._rideDates.push(rideDate);
        this._participantsData.push(participants);
        this._durationData.push(duration);
        this._distanceData.push(distance);
    };

    CityStatisticPage.prototype.createChart = function ($element) {
        var datasets = [];

        datasets.push(this._createDataset('Teilnehmer', 'participants', 'rgba(255, 0, 0, 1)', this._participantsData));
        datasets.push(this._createDataset('Fahrtlänge', 'distance', 'rgba(0, 255, 0, 1)', this._distanceData));
        datasets.push(this._createDataset('Fahrtdauer', 'duration', 'rgba(0, 0, 255, 1)', this._durationData));

        var data = {
            labels: this._rideDates,
            datasets: datasets
        };

        this._participantsChart = new Chart($element, {
            type: 'line',
            data: data,
            options: {
                scales: {
                    yAxes: [{
                        id: 'participants',
                        type: 'linear',
                        position: 'left'
                    }, {
                        id: 'distance',
                        type: 'linear',
                        position: 'right'
                    }, {
                        id: 'duration',
                        type: 'linear',
                        position: 'right'
                    }
                    ]
                }
            }
        });
    };

    CityStatisticPage.prototype._createDataset = function (label, yAxisID, colorString, data) {
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
            yAxisID: yAxisID
        }
    };

    return CityStatisticPage;
});