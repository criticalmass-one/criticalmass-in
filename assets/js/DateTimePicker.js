import flatpickr from "flatpickr";

export default class DateTimePicker {
    constructor(element, options) {
        const defaults = {};

        this.settings = {...defaults, ...options};

        this.init();
    }

    init() {
        flatpickr.l10ns.default.firstDayOfWeek = 1; // Monday

        flatpickr('.datepicker', {
            dateFormat: 'd.m.Y',
            allowInput: true,
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const element = document.querySelector('.datepicker');

    new DateTimePicker(element);
});
