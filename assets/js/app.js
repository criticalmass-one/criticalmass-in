import '../scss/criticalmass.scss';

import bootstrap from 'bootstrap';
import CookieNotice from '../js/modules/CookieNotice.js';
import AutoMap from '../js/modules/map/AutoMap.js';
import CalendarPage from '../js/modules/page/CalendarPage.js';
import CookieNotice from '../js/modules/CookieNotice.js';

window.bootstrap = bootstrap;

new CookieNotice();
window.CookieNotice = CookieNotice;

window.AutoMap = AutoMap;
window.CalendarPage = CalendarPage;
