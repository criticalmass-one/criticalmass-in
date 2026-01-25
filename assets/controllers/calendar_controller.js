import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['rideList'];

    connect() {
        this.init();
    }

    init() {
        this.rideListTargets.forEach(rideList => {
            const outerHeight = this.getOuterHeight(rideList);
            const scrollHeight = rideList.scrollHeight;

            if (scrollHeight > outerHeight) {
                rideList.classList.add('shadow');
            }
        });
    }

    /**
     * @see https://blog.jiniworld.me/80#a03-2
     */
    getInnerHeight(el) {
        const c = window.getComputedStyle(el);
        const border = parseFloat(c.borderTopWidth) + parseFloat(c.borderBottomWidth);

        return el.offsetHeight - border;
    }

    /**
     * @see https://blog.jiniworld.me/80#a03-2
     */
    getOuterHeight(el, includeMargin = false) {
        const c = window.getComputedStyle(el);
        const margin = parseFloat(c.marginTop) + parseFloat(c.marginBottom);
        const border = parseFloat(c.borderTopWidth) + parseFloat(c.borderBottomWidth);
        const scrollBar = el.offsetHeight - el.clientHeight - border;

        if (includeMargin) {
            if (c.boxSizing === 'border-box') {
                return el.offsetHeight + margin;
            } else {
                return el.offsetHeight + margin - scrollBar;
            }
        }

        return el.offsetHeight;
    }
}
