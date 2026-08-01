/*
 * Matomo (selbst gehostet, cookielos, IP-anonymisiert — siehe Datenschutzerklärung).
 * Als externe Datei statt Inline-Snippet, damit die CSP mit `script-src 'self'`
 * ohne unsafe-inline/Nonce auskommt.
 */
var _paq = window._paq = window._paq || [];
_paq.push(['disableCookies']);
_paq.push(['trackPageView']);
_paq.push(['enableLinkTracking']);
(function () {
    var u = 'https://matomo.caldera.cc/';
    _paq.push(['setTrackerUrl', u + 'matomo.php']);
    _paq.push(['setSiteId', '5']);
    var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
    g.async = true; g.src = u + 'matomo.js'; s.parentNode.insertBefore(g, s);
})();
