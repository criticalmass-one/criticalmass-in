define(['chartjs'], function() {
    RideStatisticPage = function(context, options) {
    };

    RideStatisticPage.prototype._propertiesData = [];
    RideStatisticPage.prototype._dateTime = [];

    RideStatisticPage.prototype._propertiesChart = null;

    RideStatisticPage.prototype.addProperty = function(dateTime, numberAttending, numberMaybe, numberDeclined, numberInterested, numberNoreply) {
        this._propertiesData[dateTime] = {
            numberAttending: numberAttending,
            numberMaybe: numberMaybe,
            numberDeclined: numberDeclined,
            numberInterested: numberInterested,
            numberNoreply: numberNoreply
        };

        this._dateTime.push(dateTime);
    };

    RideStatisticPage.prototype.createParticipantsChart = function($element) {
        var datasets = [];

        datasets.push(this._createDataset('ja', 'green', this._createDataList('numberAttending')));
        datasets.push(this._createDataset('vielleicht', 'yellow', this._createDataList('numberMaybe')));
        datasets.push(this._createDataset('nein', 'red', this._createDataList('numberDeclined')));
        //datasets.push(this._createDataset('interessiert', 'blue', this._createDataList('numberInterested')));
        datasets.push(this._createDataset('unbeantwortet', 'black', this._createDataList('numberNoreply')));

        var data = {
            labels: this._dateTime,
            datasets: datasets
        };

        this._propertiesChart = new Chart($element, {
            type: 'line',
            data: data
        });
    };

    RideStatisticPage.prototype._createDataList = function(property) {
        var dataList = [];

        for (var index in this._dateTime) {
            var dateTime = this._dateTime[index];

            dataList.push(this._propertiesData[dateTime][property]);
        }

        console.log(dataList);
        return dataList;
    };

    RideStatisticPage.prototype._createDataset = function(label, color, data) {
        return {
            label: label,
            fill: false,
            backgroundColor: color,
            borderColor: color,
            borderCapStyle: 'butt',
            borderDash: [],
            borderDashOffset: 0.0,
            borderJoinStyle: 'miter',
            pointBorderColor: color,
            pointBackgroundColor: "#fff",
            pointBorderWidth: 1,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: color,
            pointHoverBorderColor: color,
            pointHoverBorderWidth: 2,
            tension: 0.1,
            data: data,
            yAxisID: "y-axis-0",
        };
    };

    return RideStatisticPage;
});