import 'chart.js';

export default class StatisticCityPage {
    cities = [];
    rideMonths = [];

    constructor(chart) {
        this.createChart(chart);
    }

    createChart(element) {
        const datasets = [];

        datasets.push(this.createDataset('Teilnehmer', 'participants', 'rgba(255, 0, 0, 1)', this.collectParticipantsData()));
        datasets.push(this.createDataset('Fahrtlänge', 'distance', 'rgba(0, 255, 0, 1)', this.collectDistanceData()));
        datasets.push(this.createDataset('Fahrtdauer', 'duration', 'rgba(0, 0, 255, 1)', this.collectDurationData()));

        const data = {
            labels: this.collectRideDates(),
            datasets: datasets
        };

        const chart = new Chart(element, {
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

    createDataset(label, yAxisID, colorString, data) {
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

    collectRideDates() {
        const list = [];

        document.querySelectorAll('.ride-data').forEach((rideDataElement) => {
            const dateTime = rideDataElement.dataset.rideDatetime;

            list.push(dateTime);
        });

        return list;
    }

    collectParticipantsData() {
        const list = [];

        document.querySelectorAll('.ride-data').forEach((rideDataElement) => {
            const participants = rideDataElement.dataset.estimatedParticipants;

            list.push(participants);
        });

        return list;
    }

    collectDurationData() {
        const list = [];

        document.querySelectorAll('.ride-data').forEach((rideDataElement) => {
            const duration = rideDataElement.dataset.estimatedDuration;

            list.push(duration);
        });

        return list;
    }

    collectDistanceData() {
        const list = [];

        document.querySelectorAll('.ride-data').forEach((rideDataElement) => {
            const distance = rideDataElement.dataset.estimatedDistance;

            list.push(distance);
        });

        return list;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const chart = document.querySelector('canvas#chart');

    if (chart) {
        new StatisticCityPage(chart);
    }
});
