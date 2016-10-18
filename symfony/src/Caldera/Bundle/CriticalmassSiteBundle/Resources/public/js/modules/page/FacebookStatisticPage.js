define(['chartjs'], function () {
    FacebookStatisticPage = function (context, options) {
    };

    FacebookStatisticPage.prototype._days = [];

    FacebookStatisticPage.prototype._facebookLikes = [];

    FacebookStatisticPage.prototype._cities = [];

    FacebookStatisticPage.prototype._facebookLikesChart = null;


    FacebookStatisticPage.prototype.addDay = function (day) {
        this._days.push(day);
    };

    FacebookStatisticPage.prototype.addCity = function (cityName, citySlug, colorRed, colorGreen, colorBlue) {
        this._facebookLikes[citySlug] = [];

        this._cities[citySlug] = [
            cityName,
            colorRed,
            colorGreen,
            colorBlue
        ];
    };

    FacebookStatisticPage.prototype.addData = function (citySlug, day, likes) {
        this._facebookLikes[citySlug][day] = likes;
    };

    FacebookStatisticPage.prototype.createFacebookLikesChart = function ($element) {
        var datasets = this._createDatasets();

        var data = {
            labels: this._days,
            datasets: datasets
        };

        this._facebookLikesChart = new Chart($element, {
            type: 'line',
            data: data
        });
    };

    FacebookStatisticPage.prototype._createDatasets = function () {
        var datasets = [];

        for (var citySlug in this._cities) {
            var city = this._cities[citySlug];

            var data = [];

            for (var index in this._days) {
                var day = this._days[index];
                var likes = this._facebookLikes[citySlug][day];

                data.push(likes);

                console.log(citySlug, day, likes);
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

    return FacebookStatisticPage;
});