import './bootstrap.js';
import './scss/criticalmass.scss';
import 'dropzone/dist/dropzone.css';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
// Font Awesome als Stylesheet und Webfont, nicht als JavaScript.
//
// Die JS-Fassung bettet jedes Icon einzeln als SVG ein und tauscht die
// <i>-Elemente zur Laufzeit aus. Das kostete hier 2,97 MB, die der Browser
// auswerten musste, bevor irgendetwas reagierte — regular allein 1,39 MB.
//
// Die CSS-Fassung sind rund 91 KB Stile plus drei Schriftdateien von zusammen
// 374 KB, die der Browser zwischenspeichert und gar nicht erst auswertet. Die
// Auszeichnung im Markup bleibt dieselbe: <i class="far fa-save"> versteht
// jede der beiden.
//
// Bewusst einzeln statt all.css: Verwendet werden nur far (402x), fas (30x)
// und fab (3x). Light, Duotone und Thin waeren rund 80 KB Stile und zwei
// weitere Schriften fuer nichts.
import '@fortawesome/fontawesome-pro/css/fontawesome.css';
import '@fortawesome/fontawesome-pro/css/regular.css';
import '@fortawesome/fontawesome-pro/css/solid.css';
import '@fortawesome/fontawesome-pro/css/brands.css';

// Das Captcha-Widget steht auf genau einer Seite — der Anmeldung. Eifrig
// eingebunden waren das 381 KB auf jeder anderen Seite fuer nichts.
//
// Die Skripte laufen mit `defer`, das Dokument ist also fertig geparst, wenn
// diese Zeile ausgefuehrt wird; die Abfrage findet das Element zuverlaessig.
if (document.querySelector('.frc-captcha')) {
    import('friendly-challenge/widget');
}
