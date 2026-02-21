import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        message: String
    }

    disable(event) {
        const form = this.element.closest('form');

        if (form && form.checkValidity()) {
            if (this.messageValue) {
                this.element.textContent = this.messageValue;
            }

            this.element.disabled = true;
            form.submit();
        }
    }
}
