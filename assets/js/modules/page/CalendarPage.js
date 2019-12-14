'use strict';

const CalendarPage = function (selector) {
    const $calendar = $(selector);
    
    $calendar.find('.day').each(function () {
        const $day = $(this);
        const $rideList = $day.find('ul.ride-list');

        if ($rideList.length) {
            const outerHeight = $day.innerHeight();
            const innerheight = $rideList[0].scrollHeight;

            if (innerheight > outerHeight) {
                $day.addClass('shadow');
            }
        }
    });
};

export default CalendarPage;
