import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto';

export default class extends Controller {
    static targets = ['participantsCanvas', 'durationCanvas', 'distanceCanvas', 'rideMonth', 'city', 'rideData'];

    cities = [];
    rideMonths = [];

    connect() {
        this.initData();
        this.createParticipantsChart();
        this.createDurationChart();
        this.createDistanceChart();
    }

    disconnect() {
        if (this.participantsChart) this.participantsChart.destroy();
        if (this.durationChart) this.durationChart.destroy();
        if (this.distanceChart) this.distanceChart.destroy();
    }

    initData() {
        this.rideMonths = this.rideMonthTargets.map(el => el.dataset.rideMonth);

        this.cities = this.cityTargets.map(el => ({
            citySlug: el.dataset.citySlug,
            cityName: el.dataset.cityName,
            colorRed: el.dataset.colorRed,
            colorGreen: el.dataset.colorGreen,
            colorBlue: el.dataset.colorBlue
        }));
    }

    collectParticipantsData() {
        return this.rideDataTargets.map(el => ({
            citySlug: el.dataset.citySlug,
            rideMonth: el.dataset.rideMonth,
            value: el.dataset.estimatedParticipants
        }));
    }

    collectDurationData() {
        return this.rideDataTargets.map(el => ({
            citySlug: el.dataset.citySlug,
            rideMonth: el.dataset.rideMonth,
            value: el.dataset.estimatedDuration
        }));
    }

    collectDistanceData() {
        return this.rideDataTargets.map(el => ({
            citySlug: el.dataset.citySlug,
            rideMonth: el.dataset.rideMonth,
            value: el.dataset.estimatedDistance
        }));
    }

    createParticipantsChart() {
        if (!this.hasParticipantsCanvasTarget) return;

        const datasets = this.createDatasets(this.collectParticipantsData());

        this.participantsChart = new Chart(this.participantsCanvasTarget, {
            type: 'line',
            data: {
                labels: this.rideMonths,
                datasets: datasets
            }
        });
    }

    createDurationChart() {
        if (!this.hasDurationCanvasTarget) return;

        const datasets = this.createDatasets(this.collectDurationData());

        this.durationChart = new Chart(this.durationCanvasTarget, {
            type: 'line',
            data: {
                labels: this.rideMonths,
                datasets: datasets
            }
        });
    }

    createDistanceChart() {
        if (!this.hasDistanceCanvasTarget) return;

        const datasets = this.createDatasets(this.collectDistanceData());

        this.distanceChart = new Chart(this.distanceCanvasTarget, {
            type: 'line',
            data: {
                labels: this.rideMonths,
                datasets: datasets
            }
        });
    }

    createDatasets(list) {
        return this.cities.map(city => {
            const data = this.rideMonths.map(rideMonth => {
                const item = list.find(i => i.rideMonth === rideMonth && i.citySlug === city.citySlug);
                return item ? item.value : null;
            });

            const colorString = `rgba(${city.colorRed}, ${city.colorGreen}, ${city.colorBlue}, 1)`;

            return {
                label: city.cityName,
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
                data: data
            };
        });
    }
}
