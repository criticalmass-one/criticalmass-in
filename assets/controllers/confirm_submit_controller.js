import { Controller } from '@hotwired/stimulus';

/**
 * Fragt vor dem Absenden nach. Als Stimulus-Controller statt als onsubmit-Attribut,
 * weil die Content-Security-Policy dieser Seite kein 'unsafe-inline' für Skripte
 * erlaubt — ein Inline-Handler würde beim Scharfschalten stillschweigend ausfallen.
 */
export default class extends Controller {
    static values = { message: String };

    confirm(event) {
        if (!window.confirm(this.messageValue)) {
            event.preventDefault();
        }
    }
}
