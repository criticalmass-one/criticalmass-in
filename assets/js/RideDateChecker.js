export default class RideDateChecker {
    constructor(dateElement, timeElement, options) {
        const defaults = {};
        const that = this;

        this.settings = {...defaults, ...options};


        document.addEventListener('change', (event) => {
            const target = event.target;

            if (target.matches('input[type="date"].check-ride-date,input[type="time"].check-ride-date')) {
                that.checkRideDate();
            }
        });
    }

    checkRideDate() {
        const dateElement = document.getElementById('ride_dateTime_date');
        const date = new Date(dateElement.value);

        const messageDoubleMonthRide = document.getElementById('doubleMonthRide');
        const messageDoubleDayRide = document.getElementById('doubleDayRide');
        const submitButton = document.getElementById('rideSubmitButton');

        // there is already a ride for this day
        const daySuccessCallback = function () {
            messageDoubleMonthRide.classList.add('d-none', 'hidden');
            messageDoubleDayRide.classList.remove('d-none', 'hidden');
            submitButton.disabled = true;
        };

        // there is already a ride for this month
        const monthSuccessCallback = function () {
            messageDoubleMonthRide.classList.remove('d-none', 'hidden');
            messageDoubleDayRide.classList.add('d-none', 'hidden');
            submitButton.disabled = true;

            this.searchForMonthDay(date, daySuccessCallback);
        }.bind(this);

        // everything is fine
        const monthFailCallback = function () {
            messageDoubleMonthRide.classList.add('d-none', 'hidden');
            messageDoubleDayRide.classList.add('d-none', 'hidden');
            submitButton.disabled = false;
        }.bind(this);

        if (this.isSelfDay(date) || this.isSelfMonth(date)) {
            monthFailCallback();

            return;
        }

        this.searchForMonth(date, monthSuccessCallback, monthFailCallback);
    };

    searchForMonth(date, successCallback, errorCallback) {
        const rideDate = [date.getFullYear(), date.getMonth() + 1].join('-');
        const url = Routing.generate('caldera_criticalmass_rest_ride_show', {
            citySlug: this.getCitySlug(),
            rideIdentifier: rideDate
        });

        this.urlExists(url, successCallback, errorCallback);
    }

    searchForMonthDay(date, successCallback, errorCallback) {
        const rideDate = [date.getFullYear(), date.getMonth() + 1, date.getDate()].join('-');
        const url = Routing.generate('caldera_criticalmass_rest_ride_show', {
            citySlug: this.getCitySlug(),
            rideIdentifier: rideDate
        });

        this.urlExists(url, successCallback, errorCallback);
    }

    isSelfDay(date) {
        return (
            this.getRideDate()
            && this.getRideDate().getFullYear() === date.getFullYear()
            && this.getRideDate().getMonth() === date.getMonth()
            && this.getRideDate().getDate() === date.getDate()
        );
    }

    isSelfMonth(date) {
        return (
            this.getRideDate()
            && this.getRideDate().getFullYear() === date.getFullYear()
            && this.getRideDate().getMonth() === date.getMonth()
        );
    }

    getCitySlug() {
        const locationParts = (window.location.pathname.split('/'));
        const citySlug = locationParts[1];

        return citySlug;
    }

    getRideDate() {
        const locationParts = (window.location.pathname.split('/'));
        const rideDate = locationParts[2];
        const date = new Date(rideDate);

        return date;
    }

    urlExists(url, successCallback, failureCallback) {
        const http = new XMLHttpRequest();

        http.open('HEAD', url);
        http.onreadystatechange = function () {
            if (this.readyState === this.DONE && this.status === 200) {
                successCallback();
            } else {
                if (failureCallback) {
                    failureCallback();
                }
            }
        };

        http.send();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const dateElement = document.querySelectorAll('input[type="date"].check-ride-date');
    const timeElement = document.querySelectorAll('input[type="time"].check-ride-date');

    if (dateElement && timeElement) {
        new RideDateChecker(dateElement, timeElement);
    }
});
