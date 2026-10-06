import { Controller } from '@hotwired/stimulus';

/**
 * Werkzeugleiste, Tastenkürzel und Vorschau für die Markdown-Felder des Forums.
 *
 * Die Vorschau rendert der Server mit demselben Parser wie den fertigen Beitrag —
 * ein zweiter Renderer im Browser würde über kurz oder lang abweichen.
 */
export default class extends Controller {
    static targets = ['input', 'preview', 'writePane', 'previewPane', 'writeTab', 'previewTab'];
    static values = { previewUrl: String };

    connect() {
        this.keyHandler = this.handleKeydown.bind(this);
        this.inputTarget.addEventListener('keydown', this.keyHandler);
    }

    disconnect() {
        this.inputTarget.removeEventListener('keydown', this.keyHandler);
    }

    handleKeydown(event) {
        if (!event.ctrlKey && !event.metaKey) {
            return;
        }

        const shortcuts = { b: 'bold', i: 'italic', k: 'link' };
        const action = shortcuts[event.key.toLowerCase()];

        if (!action) {
            return;
        }

        event.preventDefault();
        this.applyFormat(action);
    }

    format(event) {
        event.preventDefault();
        this.applyFormat(event.currentTarget.dataset.format);
    }

    applyFormat(format) {
        const input = this.inputTarget;
        const start = input.selectionStart;
        const end = input.selectionEnd;
        const selected = input.value.slice(start, end);

        const wraps = {
            bold: ['**', '**', 'fetter Text'],
            italic: ['_', '_', 'kursiver Text'],
            code: ['`', '`', 'Code'],
            link: ['[', '](https://)', 'Linktext'],
        };
        const prefixes = {
            list: '- ',
            quote: '> ',
            codeblock: '```\n',
        };

        let replacement;
        let caret;

        if (wraps[format]) {
            const [before, after, placeholder] = wraps[format];
            const text = selected || placeholder;

            replacement = before + text + after;
            caret = start + before.length + text.length;
        } else if (prefixes[format]) {
            const text = selected || (format === 'codeblock' ? 'Code' : 'Zeile');
            const prefix = prefixes[format];

            replacement = format === 'codeblock'
                ? `${prefix}${text}\n\`\`\``
                : text.split('\n').map((line) => prefix + line).join('\n');
            caret = start + replacement.length;
        } else {
            return;
        }

        input.setRangeText(replacement, start, end, 'end');
        input.setSelectionRange(caret, caret);
        input.focus();
    }

    /**
     * Fügt ein Zitat am Anfang des Feldes ein — ausgelöst vom „Zitieren“-Knopf eines Beitrags.
     */
    quote({ detail }) {
        if (!detail || !detail.text) {
            return;
        }

        const quoted = detail.text.split('\n').map((line) => `> ${line}`).join('\n');
        const attribution = detail.author ? `**${detail.author}** schrieb:\n` : '';
        const existing = this.inputTarget.value;

        this.inputTarget.value = `${attribution}${quoted}\n\n${existing}`;
        this.inputTarget.focus();
        this.inputTarget.setSelectionRange(this.inputTarget.value.length, this.inputTarget.value.length);
    }

    showWrite(event) {
        event.preventDefault();
        this.togglePanes(true);
    }

    async showPreview(event) {
        event.preventDefault();
        this.togglePanes(false);

        this.previewTarget.innerHTML = '<p class="text-muted mb-0">Vorschau wird geladen …</p>';

        try {
            const response = await fetch(this.previewUrlValue, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ message: this.inputTarget.value }),
            });

            if (!response.ok) {
                throw new Error(String(response.status));
            }

            this.previewTarget.innerHTML = await response.text();
        } catch (error) {
            this.previewTarget.innerHTML =
                '<p class="text-danger mb-0">Die Vorschau lässt sich gerade nicht laden. Dein Text bleibt erhalten.</p>';
        }
    }

    togglePanes(showWrite) {
        this.writePaneTarget.hidden = !showWrite;
        this.previewPaneTarget.hidden = showWrite;

        this.writeTabTarget.classList.toggle('active', showWrite);
        this.previewTabTarget.classList.toggle('active', !showWrite);
        this.writeTabTarget.setAttribute('aria-selected', String(showWrite));
        this.previewTabTarget.setAttribute('aria-selected', String(!showWrite));
    }
}
