import '../scss/criticalmass.scss';


import CC from 'CookieConsent'

//window.bootstrap = bootstrap;
require('bootstrap');

import DateTimePicker from './DateTimePicker';
import GeocodingButton from './GeocodingButton';
import Map from './Map';
import DataTable from './DataTable';

document.addEventListener('DOMContentLoaded', function() {
    window.cookieconsent.initialise({
        "palette": {
            "popup": {
                "background": "#64386b",
                "text": "#ffcdfd"
            },
            "button": {
                "background": "#f8a8ff",
                "text": "#3f0045"
            }
        }
    });

    const calendarDayList = document.querySelectorAll('#calendar .day ul.ride-list');

    calendarDayList.forEach(function(rideList) {
        const outerHeight = getOuterHeight(rideList);
        const scrollHeight = rideList.scrollHeight;

        if (scrollHeight > outerHeight) {
            rideList.classList.add('shadow');
        }
    });
});

/**
 * @see https://blog.jiniworld.me/80#a03-2
 */
function getInnerHeight(el) {
    var c = window.getComputedStyle(el);
    var border = parseFloat(c.borderTopWidth) + parseFloat(c.borderBottomWidth);

    return el.offsetHeight - border;
}

/**
 * @see https://blog.jiniworld.me/80#a03-2
 */
function getOuterHeight(el, includeMargin) {
    includeMargin = includeMargin || false;
    var c = window.getComputedStyle(el);
    var margin = parseFloat(c.marginTop) + parseFloat(c.marginBottom),
        border = parseFloat(c.borderTopWidth) + parseFloat(c.borderBottomWidth);
    var scrollBar = el.offsetHeight - el.clientHeight - border;
    if(includeMargin) {
        if(c.boxSizing == "border-box") {
            return el.offsetHeight + margin;
        } else {
            return el.offsetHeight + margin - scrollBar;
        }
    }
    return el.offsetHeight;
}

