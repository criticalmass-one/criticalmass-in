define([], function () {
    CalendarPage = function (selector) {
        var $calendar = $(selector);

        $calendar.find('.day').each(function() {
            var $day = $(this);
            var $rideList = $day.find('ul.ride-list');

            if ($rideList.length) {
                var outerHeight = $day.innerHeight();
                var innerheight = $rideList.outerHeight();

                console.log('foo', outerHeight, innerheight);
            }
        });
    };

    return CalendarPage;
});