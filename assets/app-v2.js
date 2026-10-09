// Einstieg der neuen Ansicht (templates/v2/). Laedt dieselben Stimulus-Controller
// wie app.js, damit Karten, Suche und Formular-Helfer auch hier laufen, aber ein
// eigenes Stylesheet ohne die alte Kompatibilitaetsschicht.
import './bootstrap.js';
import './scss/v2/app.scss';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Font Awesome bleibt vorerst drin: wiederverwendete Partials und Stimulus-Vorlagen
// nutzen noch <i class="far …">. Neue Templates nehmen das SVG-Sprite aus
// templates/v2/Layout/_icons.html.twig. Begruendung der CSS-Fassung: siehe app.js.
import '@fortawesome/fontawesome-pro/css/fontawesome.css';
import '@fortawesome/fontawesome-pro/css/regular.css';
import '@fortawesome/fontawesome-pro/css/solid.css';
import '@fortawesome/fontawesome-pro/css/brands.css';

if (document.querySelector('.frc-captcha')) {
    import('friendly-challenge/widget');
}
