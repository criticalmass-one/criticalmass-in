import 'chart.js';

export default class StatisticPage {
    cities = [];
    rideMonths = [];

    constructor(participants, duration, distance) {
        this.initData();

        this.createParticipantsChart(participants);
        this.createDurationChart(duration);
        this.createDistanceChart(distance);
    }

    initData() {
        document.querySelectorAll('.ride-month').forEach((rideMonthElement) => {
            const rideMonth = rideMonthElement.dataset.rideMonth;

            this.rideMonths.push(rideMonth);
        });

        document.querySelectorAll('.city').forEach((cityElement) => {
            const cityName = cityElement.dataset.cityName;
            const citySlug = cityElement.dataset.citySlug;
            const colorRed = cityElement.dataset.colorRed;
            const colorGreen = cityElement.dataset.colorGreen;
            const colorBlue = cityElement.dataset.colorBlue;

            this.addCity(cityName, citySlug, colorRed, colorGreen, colorBlue);
        });
    }

    collectParticipantsData() {
        const list = [];

        document.querySelectorAll('.ride-data').forEach((rideDataElement) => {
            const citySlug = rideDataElement.dataset.citySlug;
            const rideMonth = rideDataElement.dataset.rideMonth;
            const participants = rideDataElement.dataset.estimatedParticipants;

            list.push({
                citySlug: citySlug,
                rideMonth: rideMonth,
                value: participants
            });
        });

        return list;
    }

    collectDurationData() {
        const list = [];

        document.querySelectorAll('.ride-data').forEach((rideDataElement) => {
            const citySlug = rideDataElement.dataset.citySlug;
            const rideMonth = rideDataElement.dataset.rideMonth;
            const duration = rideDataElement.dataset.estimatedDuration;

            list.push({
                citySlug: citySlug,
                rideMonth: rideMonth,
                value: duration
            });
        });

        return list;
    }

    collectDistanceData() {
        const list = [];

        document.querySelectorAll('.ride-data').forEach((rideDataElement) => {
            const citySlug = rideDataElement.dataset.citySlug;
            const rideMonth = rideDataElement.dataset.rideMonth;
            const distance = rideDataElement.dataset.estimatedDistance;

            list.push({
                citySlug: citySlug,
                rideMonth: rideMonth,
                value: distance
            });
        });

        return list;
    }

    addCity(cityName, citySlug, colorRed, colorGreen, colorBlue) {
        this.cities.push({
            citySlug: citySlug,
            cityName: cityName,
            colorRed: colorRed,
            colorGreen: colorGreen,
            colorBlue: colorBlue
        });
    };

    createParticipantsChart(element) {
        const datasets = this.createDataset(this.collectParticipantsData());

        const data = {
            labels: this.rideMonths,
            datasets: datasets
        };

        this.participantsChart = new Chart(element, {
            type: 'line',
            data: data
        });
    };

    createDistanceChart(element) {
        const datasets = this.createDataset(this.collectDistanceData());

        const data = {
            labels: this.rideMonths,
            datasets: datasets
        };

        this.distanceChart = new Chart(element, {
            type: 'line',
            data: data
        });
    };

    createDurationChart(element) {
        const datasets = this.createDataset(this.collectDurationData());

        const data = {
            labels: this.rideMonths,
            datasets: datasets
        };

        this.durationChart = new Chart(element, {
            type: 'line',
            data: data
        });
    };

    createDataset(list) {
        const datasets = [];

        this.cities.forEach((city) => {
            const data = [];

            this.rideMonths.forEach((rideMonth) => {
                list.forEach((listItem) => {
                    if (listItem.rideMonth === rideMonth && listItem.citySlug === city.citySlug) {
                        data.push(listItem.value);
                    }
                })
            });

            const colorString = 'rgba(' + city.colorRed + ', ' + city.colorGreen + ', ' + city.colorBlue + ', 1)';

            datasets.push({
                label: city.cityName,
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
        });

        return datasets;
    };
}

document.addEventListener('DOMContentLoaded', () => {
    const participants = document.querySelector('canvas#participants');
    const duration = document.querySelector('canvas#duration');
    const distance = document.querySelector('canvas#distance');

    if (participants) {
        new StatisticPage(participants, duration, distance);
    }
});
