import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto';

export default class extends Controller {
    static targets = ['canvas', 'rideData'];

    connect() {
        this.createChart();
    }

    disconnect() {
        if (this.chart) {
            this.chart.destroy();
        }
    }

    createChart() {
        const datasets = [];

        datasets.push(this.createDataset('Teilnehmer', 'participants', 'rgba(255, 0, 0, 1)', this.collectParticipantsData()));
        datasets.push(this.createDataset('Fahrtlänge', 'distance', 'rgba(0, 255, 0, 1)', this.collectDistanceData()));
        datasets.push(this.createDataset('Fahrtdauer', 'duration', 'rgba(0, 0, 255, 1)', this.collectDurationData()));

        const data = {
            labels: this.collectRideDates(),
            datasets: datasets
        };

        this.chart = new Chart(this.canvasTarget, {
            type: 'line',
            data: data,
            options: {
                scales: {
                    participants: {
                        type: 'linear',
                        position: 'left'
                    },
                    distance: {
                        type: 'linear',
                        position: 'right'
                    },
                    duration: {
                        type: 'linear',
                        position: 'right'
                    }
                }
            }
        });
    }

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
            pointBackgroundColor: '#fff',
            pointBorderWidth: 1,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: colorString,
            pointHoverBorderColor: colorString,
            pointHoverBorderWidth: 2,
            tension: 0.1,
            data: data,
            yAxisID: yAxisID
        };
    }

    collectRideDates() {
        return this.rideDataTargets.map(el => el.dataset.rideDatetime);
    }

    collectParticipantsData() {
        return this.rideDataTargets.map(el => el.dataset.estimatedParticipants);
    }

    collectDurationData() {
        return this.rideDataTargets.map(el => el.dataset.estimatedDuration);
    }

    collectDistanceData() {
        return this.rideDataTargets.map(el => el.dataset.estimatedDistance);
    }
}
