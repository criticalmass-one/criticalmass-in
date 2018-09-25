define([], function () {
    CalendarPage = function (selector) {
        var $calendar = $(selector);

        $calendar.find('.day').each(function () {
            var $day = $(this);
            var $rideList = $day.find('ul.ride-list');

            if ($rideList.length) {
                var outerHeight = $day.innerHeight();
                var innerheight = $rideList[0].scrollHeight;

                if (innerheight > outerHeight) {
                    $day.addClass('shadow');
                }
            }
        });
    };

    return CalendarPage;
});
