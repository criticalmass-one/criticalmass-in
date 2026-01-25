import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['date', 'submitButton', 'doubleMonthMessage', 'doubleDayMessage'];

    checkDate() {
        const date = new Date(this.dateTarget.value);

        if (this.isSelfDay(date) || this.isSelfMonth(date)) {
            this.hideMessages();
            this.enableSubmit();
            return;
        }

        this.searchForMonth(date);
    }

    searchForMonth(date) {
        const rideDate = [date.getFullYear(), date.getMonth() + 1].join('-');
        const url = '/' + encodeURIComponent(this.getCitySlug()) + '/' + encodeURIComponent(rideDate);

        this.urlExists(url,
            () => this.onMonthExists(date),
            () => this.onMonthFree()
        );
    }

    onMonthExists(date) {
        this.showDoubleMonthMessage();
        this.searchForMonthDay(date);
    }

    onMonthFree() {
        this.hideMessages();
        this.enableSubmit();
    }

    searchForMonthDay(date) {
        const rideDate = [date.getFullYear(), date.getMonth() + 1, date.getDate()].join('-');
        const url = '/' + encodeURIComponent(this.getCitySlug()) + '/' + encodeURIComponent(rideDate);

        this.urlExists(url,
            () => this.onDayExists(),
            () => this.onDayFree()
        );
    }

    onDayExists() {
        this.hideDoubleMonthMessage();
        this.showDoubleDayMessage();
        this.disableSubmit();
    }

    onDayFree() {
        this.showDoubleMonthMessage();
        this.hideDoubleDayMessage();
        this.enableSubmit();
    }

    hideMessages() {
        this.hideDoubleMonthMessage();
        this.hideDoubleDayMessage();
    }

    showDoubleMonthMessage() {
        if (this.hasDoubleMonthMessageTarget) {
            this.doubleMonthMessageTarget.classList.remove('d-none', 'hidden');
        }
    }

    hideDoubleMonthMessage() {
        if (this.hasDoubleMonthMessageTarget) {
            this.doubleMonthMessageTarget.classList.add('d-none', 'hidden');
        }
    }

    showDoubleDayMessage() {
        if (this.hasDoubleDayMessageTarget) {
            this.doubleDayMessageTarget.classList.remove('d-none', 'hidden');
        }
    }

    hideDoubleDayMessage() {
        if (this.hasDoubleDayMessageTarget) {
            this.doubleDayMessageTarget.classList.add('d-none', 'hidden');
        }
    }

    enableSubmit() {
        if (this.hasSubmitButtonTarget) {
            this.submitButtonTarget.disabled = false;
        }
    }

    disableSubmit() {
        if (this.hasSubmitButtonTarget) {
            this.submitButtonTarget.disabled = true;
        }
    }

    isSelfDay(date) {
        const rideDate = this.getRideDate();
        return rideDate &&
            rideDate.getFullYear() === date.getFullYear() &&
            rideDate.getMonth() === date.getMonth() &&
            rideDate.getDate() === date.getDate();
    }

    isSelfMonth(date) {
        const rideDate = this.getRideDate();
        return rideDate &&
            rideDate.getFullYear() === date.getFullYear() &&
            rideDate.getMonth() === date.getMonth();
    }

    getCitySlug() {
        const locationParts = window.location.pathname.split('/');
        return locationParts[1];
    }

    getRideDate() {
        const locationParts = window.location.pathname.split('/');
        const rideDate = locationParts[2];
        return rideDate ? new Date(rideDate) : null;
    }

    urlExists(url, successCallback, failureCallback) {
        fetch(url, { method: 'HEAD' })
            .then(response => {
                if (response.ok) {
                    successCallback();
                } else {
                    failureCallback();
                }
            })
            .catch(() => failureCallback());
    }
}
