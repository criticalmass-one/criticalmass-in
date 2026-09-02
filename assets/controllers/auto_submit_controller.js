import { Controller } from '@hotwired/stimulus';

/**
 * Schickt das Formular ab, sobald sich ein Feld ändert — für Schalter, bei denen ein
 * eigener Speichern-Knopf nur im Weg steht.
 *
 * Ohne Javascript bleibt der Knopf im noscript-Block der einzige Weg; deshalb wird er
 * hier ausgeblendet statt im Markup zu fehlen.
 */
export default class extends Controller {
    submit() {
        this.element.requestSubmit();
    }
}
