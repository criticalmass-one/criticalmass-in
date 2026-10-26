import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';

/**
 * Der „Zitieren“-Knopf eines Beitrags: Er reicht den Text an das Antwortfeld weiter
 * und öffnet, falls das Feld in einem Dialog liegt, diesen Dialog.
 */
export default class extends Controller {
    static values = { author: String, target: String, modal: String };

    quote(event) {
        event.preventDefault();

        const editor = document.querySelector(this.targetValue);

        if (!editor) {
            return;
        }

        const text = this.element.dataset.quoteText || '';

        editor.dispatchEvent(new CustomEvent('markdown-editor:quote', {
            detail: { text, author: this.authorValue },
        }));

        if (this.hasModalValue) {
            const dialog = document.querySelector(this.modalValue);

            if (dialog) {
                Modal.getOrCreateInstance(dialog).show();
            }
        }
    }
}
