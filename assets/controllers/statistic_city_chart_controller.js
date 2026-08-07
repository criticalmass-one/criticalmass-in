import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto';

const rideTypeColors = {
    CRITICAL_MASS: 'rgba(255, 0, 0, 1)',
    KIDICAL_MASS: 'rgba(255, 165, 0, 1)',
    NIGHT_RIDE: 'rgba(128, 0, 128, 1)',
    LUNCH_RIDE: 'rgba(0, 128, 0, 1)',
    DAWN_RIDE: 'rgba(255, 200, 0, 1)',
    DUSK_RIDE: 'rgba(100, 100, 200, 1)',
    DEMONSTRATION: 'rgba(200, 50, 50, 1)',
    ALLEYCAT: 'rgba(0, 200, 200, 1)',
    TOUR: 'rgba(150, 100, 50, 1)',
    EVENT: 'rgba(200, 100, 200, 1)',
};

const rideTypeLabels = {
    CRITICAL_MASS: 'Critical Mass',
    KIDICAL_MASS: 'Kidical Mass',
    NIGHT_RIDE: 'Nightride',
    LUNCH_RIDE: 'Lunch Ride',
    DAWN_RIDE: 'Dawn Ride',
    DUSK_RIDE: 'Dusk Ride',
    DEMONSTRATION: 'Demonstration',
    ALLEYCAT: 'Alleycat',
    TOUR: 'Tour',
    EVENT: 'Event',
};

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
        const dates = this.collectRideDates();
        const rideTypes = this.collectRideTypes();
        const uniqueTypes = [...new Set(rideTypes)];

        const datasets = [];

        for (const type of uniqueTypes) {
            const color = rideTypeColors[type] || 'rgba(128, 128, 128, 1)';
            const label = uniqueTypes.length > 1
                ? `Teilnehmer — ${rideTypeLabels[type] || type}`
                : 'Teilnehmer';

            const data = this.rideDataTargets.map((el, i) =>
                rideTypes[i] === type ? el.dataset.estimatedParticipants : null
            );

            datasets.push(this.createDataset(label, 'participants', color, data));
        }

        datasets.push(this.createDataset('Fahrtlänge', 'distance', 'rgba(0, 255, 0, 1)', this.collectDistanceData()));
        datasets.push(this.createDataset('Fahrtdauer', 'duration', 'rgba(0, 0, 255, 1)', this.collectDurationData()));

        const data = {
            labels: dates,
            datasets: datasets
        };

        this.chart = new Chart(this.canvasTarget, {
            type: 'line',
            data: data,
            options: {
                spanGaps: false,
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

    collectRideTypes() {
        return this.rideDataTargets.map(el => el.dataset.rideType || 'CRITICAL_MASS');
    }

    collectDurationData() {
        return this.rideDataTargets.map(el => el.dataset.estimatedDuration);
    }

    collectDistanceData() {
        return this.rideDataTargets.map(el => el.dataset.estimatedDistance);
    }
}
