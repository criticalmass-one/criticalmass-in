import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    open(event) {
        event.preventDefault();

        const url = this.element.getAttribute('href');
        window.open(url, '_blank', 'toolbar=yes,scrollbars=yes,resizable=yes,width=500,height=400');
    }
}
