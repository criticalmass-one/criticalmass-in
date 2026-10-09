import './bootstrap.js';
import './scss/v2/app.scss';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Fuer wiederverwendete Partials, die noch <i class="far …"> nutzen.
import '@fortawesome/fontawesome-pro/css/fontawesome.css';
import '@fortawesome/fontawesome-pro/css/regular.css';
import '@fortawesome/fontawesome-pro/css/solid.css';
import '@fortawesome/fontawesome-pro/css/brands.css';

if (document.querySelector('.frc-captcha')) {
    import('friendly-challenge/widget');
}
