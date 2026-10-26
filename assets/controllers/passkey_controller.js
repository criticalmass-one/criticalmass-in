import { Controller } from '@hotwired/stimulus';

/**
 * Passkeys anlegen und sich damit anmelden.
 *
 * Bewusst ohne Fremdbibliothek: Was @simplewebauthn/browser vor allem abnahm, war die
 * Umrechnung zwischen base64url und ArrayBuffer, und die erledigen die Browser inzwischen
 * selbst über parseCreationOptionsFromJSON() / parseRequestOptionsFromJSON() / toJSON().
 * Für ältere Browser steht die Umrechnung weiter unten, das ist billiger als eine
 * Dependency (package-lock.json ist eingefroren).
 */
export default class extends Controller {
    static targets = ['status', 'submit'];

    static values = {
        loginOptionsUrl: { type: String, default: '/passkey/login/options' },
        loginUrl: { type: String, default: '/passkey/login' },
        registerOptionsUrl: { type: String, default: '/passkey/register/options' },
        registerUrl: { type: String, default: '/passkey/register' },
        redirectUrl: { type: String, default: '/' },
        // Nur auf der Login-Seite: dort soll der Browser den Passkey im Autofill
        // anbieten. In der Kontoverwaltung ist der Nutzer längst angemeldet.
        conditional: { type: Boolean, default: false },
        confirmText: { type: String, default: 'Wirklich löschen?' },
    };

    connect() {
        if (!this.isSupported()) {
            this.element.hidden = true;

            return;
        }

        if (this.conditionalValue) {
            this.startConditionalMediation();
        }
    }

    disconnect() {
        this.abortConditionalMediation();
    }

    // ---------------------------------------------------------------- Registrierung

    async register(event) {
        event.preventDefault();

        // Die Conditional UI hält einen offenen get()-Aufruf. Zwei gleichzeitige
        // WebAuthn-Ceremonies verträgt der Browser nicht, also erst abräumen.
        this.abortConditionalMediation();

        try {
            this.busy(true);

            const optionsJson = await this.postJson(this.registerOptionsUrlValue, {});
            const options = this.parseCreationOptions(optionsJson);
            const credential = await navigator.credentials.create({ publicKey: options });

            await this.postJson(this.registerUrlValue, this.serialize(credential));

            window.location.reload();
        } catch (error) {
            this.busy(false);
            this.fail(error, 'Der Passkey konnte nicht angelegt werden.');
        }
    }

    // ---------------------------------------------------------------- Anmeldung

    async login(event) {
        event.preventDefault();

        this.abortConditionalMediation();

        try {
            this.busy(true);

            await this.assert();
        } catch (error) {
            this.busy(false);
            this.fail(error, 'Die Anmeldung mit dem Passkey hat nicht geklappt.');
        }
    }

    /**
     * Bietet den Passkey im Autofill des E-Mail-Feldes an, ohne dass die Seite anders
     * aussieht. Scheitert das still, bleibt der normale Weg über den Button.
     */
    async startConditionalMediation() {
        if (typeof PublicKeyCredential.isConditionalMediationAvailable !== 'function') {
            return;
        }

        try {
            if (!(await PublicKeyCredential.isConditionalMediationAvailable())) {
                return;
            }

            this.conditionalController = new AbortController();

            await this.assert({
                mediation: 'conditional',
                signal: this.conditionalController.signal,
            });
        } catch (error) {
            // Ein Abbruch ist der Normalfall: der Nutzer hat sich anders angemeldet oder
            // die Seite verlassen. Alles andere ist hier nicht behandelbar, weil im
            // Hintergrund niemand darauf wartet.
            if (error.name !== 'AbortError' && error.name !== 'NotAllowedError') {
                console.warn('[passkey] Conditional UI nicht verfügbar:', error);
            }
        }
    }

    abortConditionalMediation() {
        if (this.conditionalController) {
            this.conditionalController.abort();
            this.conditionalController = null;
        }
    }

    async assert(extraOptions = {}) {
        const optionsJson = await this.postJson(this.loginOptionsUrlValue, {});
        const options = this.parseRequestOptions(optionsJson);

        const credential = await navigator.credentials.get({
            publicKey: options,
            ...extraOptions,
        });

        await this.postJson(this.loginUrlValue, this.serialize(credential));

        window.location.href = this.redirectUrlValue;
    }

    /**
     * Rückfrage vor dem Löschen. Als Stimulus-Action statt als Inline-onclick, weil die
     * Content-Security-Policy `script-src` bewusst ohne `unsafe-inline` fährt.
     */
    confirmDelete(event) {
        if (!window.confirm(this.confirmTextValue)) {
            event.preventDefault();
        }
    }

    // ---------------------------------------------------------------- Transport

    async postJson(url, payload) {
        const response = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify(payload),
        });

        const body = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(body.errorMessage || body.message || `Der Server hat mit ${response.status} geantwortet.`);
        }

        return body;
    }

    // ---------------------------------------------------------------- Kodierung

    isSupported() {
        return typeof window.PublicKeyCredential !== 'undefined'
            && typeof navigator.credentials?.create === 'function';
    }

    parseCreationOptions(json) {
        if (typeof PublicKeyCredential.parseCreationOptionsFromJSON === 'function') {
            return PublicKeyCredential.parseCreationOptionsFromJSON(json);
        }

        return {
            ...json,
            challenge: base64UrlToBuffer(json.challenge),
            user: { ...json.user, id: base64UrlToBuffer(json.user.id) },
            excludeCredentials: (json.excludeCredentials || []).map(descriptorToBuffer),
        };
    }

    parseRequestOptions(json) {
        if (typeof PublicKeyCredential.parseRequestOptionsFromJSON === 'function') {
            return PublicKeyCredential.parseRequestOptionsFromJSON(json);
        }

        return {
            ...json,
            challenge: base64UrlToBuffer(json.challenge),
            allowCredentials: (json.allowCredentials || []).map(descriptorToBuffer),
        };
    }

    /**
     * Der Name des Passkeys läuft bewusst nicht hier mit: Den Registrierungs-Endpunkt
     * stellt das Bundle, ein zusätzliches Feld im Body erreicht das Repository nie.
     * Benannt wird serverseitig, umbenennen lässt sich in der Liste.
     */
    serialize(credential) {
        return typeof credential.toJSON === 'function'
            ? credential.toJSON()
            : legacySerialize(credential);
    }

    // ---------------------------------------------------------------- Rückmeldung

    busy(isBusy) {
        if (this.hasSubmitTarget) {
            this.submitTarget.disabled = isBusy;
        }

        if (isBusy) {
            this.clearStatus();
        }
    }

    fail(error, fallbackMessage) {
        // Der Nutzer hat den Systemdialog weggeklickt — das ist keine Fehlermeldung wert.
        if (error.name === 'NotAllowedError' || error.name === 'AbortError') {
            return;
        }

        this.showStatus(error.message || fallbackMessage);
    }

    showStatus(message) {
        if (!this.hasStatusTarget) {
            return;
        }

        this.statusTarget.textContent = message;
        this.statusTarget.hidden = false;
    }

    clearStatus() {
        if (this.hasStatusTarget) {
            this.statusTarget.textContent = '';
            this.statusTarget.hidden = true;
        }
    }
}

function base64UrlToBuffer(value) {
    const padded = value.replace(/-/g, '+').replace(/_/g, '/');
    const binary = atob(padded.padEnd(padded.length + ((4 - (padded.length % 4)) % 4), '='));
    const buffer = new Uint8Array(binary.length);

    for (let i = 0; i < binary.length; i += 1) {
        buffer[i] = binary.charCodeAt(i);
    }

    return buffer.buffer;
}

function bufferToBase64Url(buffer) {
    const bytes = new Uint8Array(buffer);
    let binary = '';

    for (let i = 0; i < bytes.length; i += 1) {
        binary += String.fromCharCode(bytes[i]);
    }

    return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
}

function descriptorToBuffer(descriptor) {
    return { ...descriptor, id: base64UrlToBuffer(descriptor.id) };
}

function legacySerialize(credential) {
    const response = credential.response;

    const serialized = {
        id: credential.id,
        rawId: bufferToBase64Url(credential.rawId),
        type: credential.type,
        clientExtensionResults: credential.getClientExtensionResults(),
        response: {
            clientDataJSON: bufferToBase64Url(response.clientDataJSON),
        },
    };

    if (response.attestationObject) {
        serialized.response.attestationObject = bufferToBase64Url(response.attestationObject);
        serialized.response.transports = typeof response.getTransports === 'function'
            ? response.getTransports()
            : [];
    } else {
        serialized.response.authenticatorData = bufferToBase64Url(response.authenticatorData);
        serialized.response.signature = bufferToBase64Url(response.signature);
        serialized.response.userHandle = response.userHandle ? bufferToBase64Url(response.userHandle) : null;
    }

    return serialized;
}
