var map, sidebar;

$('.cityRow').on('click', function()
{
    showCityInfo($(this).data('cityslug'));
});

function resetCityInfo()
{
    $('#cityModalTitle').html('Critical Mass');
    $('#cityModalTabInfoJumbotronTitle').html('Critical Mass');
    $('#cityModalTabInfoTitle').html('Critical Mass');
    $('#cityModalTabInfoDescription').html('');

    $('cityModalTabInfoJumbotron').hide();
    $('#cityModalTabNextRideJumbotron').hide();

    $('#cityModalTabInfoJumbotron').css('background-image', '');
    $('#cityModalTabNextRideJumbotron').css('background-image', '');

    $('#cityModalTabInfoSocialMedia').html('');
    $('#cityModalTabNextRideLocation span').html('Der Treffpunkt ist noch nicht bekannt.');
    $('#cityModalTabNextRideDate time').html('Das Datum ist noch nicht bekannt.');
    $('#cityModalTabNextRideTime time').html('Die Uhrzeit ist noch nicht bekannt.');

    $('#cityModalTabs li:eq(0) a').tab('show');
}

function showCityInfo(slug)
{
    var city = CityFactory.getCityFromStorageBySlug(slug);

    _paq.push(['trackEvent', 'showCityModal', slug]);

    resetCityInfo();

    $('#cityModalTabNextButtonsCity').on('click', function()
    {
        $('#cityInfoModal').modal('hide');
        map.map.invalidateSize();

        //map.map.panTo([city.getLatitude(), city.getLongitude()]);
        map.map.setView([city.getLatitude(), city.getLongitude()], 14);

        _paq.push(['trackEvent', 'panToCity', 'hamburg']);
    });
    /*var imageFilename = Url.getUrlPrefix() + 'images/city/' + slug + '.jpg';

    if (Url.fileExists(imageFilename))
    {
        $('#cityModalTabInfoJumbotron').show();
        $('#cityModalTabInfoTitle').hide();

        $('#cityModalTabInfoJumbotron').css('background-image', 'url(' + imageFilename + ')');
    }
    else
    {
        $('#cityModalTabInfoJumbotron').hide();
        $('#cityModalTabInfoTitle').show();
    }
*/
    $('#cityModalTitle').html(city.getTitle());
/*    $('#cityModalTabInfoJumbotronTitle').html(city.getTitle());
    $('#cityModalTabInfoTitle').html(city.getTitle());
    $('#cityModalTabInfoDescription').html(city.getDescription());

    var html = '';

    if (city.getUrl())
    {
        html += '<button type="button" class="btn btn-default" href="' + city.getUrl() + '">WWW</button>';
    }

    if (city.getFacebook())
    {
        html += '<button type="button" class="btn btn-default" href="' + city.getFacebook() + '">facebook</button>';
    }

    if (city.getTwitter())
    {
        html += '<button type="button" class="btn btn-default" href="' + city.getTwitter() + '">twitter</button>';
    }

    $('#cityModalTabInfoSocialMedia').html(html);
*/
    var ride = RideFactory.getRideFromStorageBySlug(slug);

    if (ride)
    {
        $('#cityModalTabNextRideKnown').show();
        $('#cityModalTabNextRideUnknown').hide();

        /*var imageFilename = Url.getUrlPrefix() + 'images/ride/' + slug + '/' + ride.getId() + '.jpg';

        if (Url.fileExists(imageFilename))
        {
            $('#cityModalTabNextRideJumbotron').show();
            $('#cityModalTabNextRideTitle').hide();

            $('#cityModalTabNextRideJumbotron').css('background-image', 'url(' + imageFilename + ')');
        }
        else
        {
            $('#cityModalTabNextRideJumbotron').hide();
            $('#cityModalTabNextRideTitle').show();
        }*/

        $('#cityModalTabNextRideJumbotronTitle').html(ride.getTitle());
        $('#cityModalTabNextRideTitle').html(ride.getTitle());
        $('#cityModalTabNextRideDescription').html(ride.getDescription());

        if (ride.getLocation())
        {
            $('#cityModalTabNextRideLocation').html(ride.getLocation());
            $('#cityModalTabNextButtonsLocation').show();

            $('#cityModalTabNextButtonsLocation').on('click', function()
            {
                $('#cityInfoModal').modal('hide');

                //map.map.panTo([ride.getLatitude(), ride.getLongitude()]);
                map.map.invalidateSize();
                map.map.setView([ride.getLatitude(), ride.getLongitude()], 14);

                _paq.push(['trackEvent', 'panToLocation', slug]);
            });
        }
        else
        {
            $('#cityModalTabNextRideLocation').html('Der Treffpunkt ist noch nicht bekannt :(');
            $('#cityModalTabNextButtonsLocation').hide();
        }

        $('#cityModalTabNextRideDate').html(ride.getFormattedDate());
        $('#cityModalTabNextRideTime').html(ride.getFormattedTime());
        $('#cityModalTabNextRideGlympse').html(slug + '@criticalmass.in');

        if (ride.getWeatherForecast())
        {
            $('#cityModalTabNextRideWeatherForecast').html(ride.getWeatherForecast());
            $('#cityModalTabNextRideWeatherForecastRow').show();
        }
        else
        {
            $('#cityModalTabNextRideWeatherForecastRow').hide();
        }


        $('#cityModalTabNextRideUnknown').hide();

        var html = '';

        if (ride.getUrl())
        {
            html += '<button type="button" class="btn btn-default" href="' + ride.getUrl() + '">WWW</button>';
        }

        if (ride.getFacebook())
        {
            html += '<button type="button" class="btn btn-default" href="' + ride.getFacebook() + '">facebook</button>';
        }

        if (ride.getTwitter())
        {
            html += '<button type="button" class="btn btn-default" href="' + ride.getTwitter() + '">twitter</button>';
        }

        // das kommt erst später wieder rein
        //$('#cityModalTabNextRideSocialMedia').html(html);

        if (map.mapPositions.getLatestPosition(slug))
        {
            $('#cityModalTabNextButtonsPosition').show();

            $('#cityModalTabNextButtonsPosition').on('click', function()
            {
                map.mapPositions.panToLatestPosition(slug)
                _paq.push(['trackEvent', 'panToPosition', slug]);
            });
        }
        else
        {
            $('#cityModalTabNextButtonsPosition').hide();
        }
    }
    else
    {
        $('#cityModalTabNextRideKnown').hide();
        $('#cityModalTabNextRideUnknown').show();
    }

    $('#cityInfoModal').modal('show');
}

if (document.body.clientWidth <= 767) {
    var isCollapsed = true;
} else {
    var isCollapsed = false;
    /*sidebar.show();*/
}

function initApp()
{
    map = new Map('map');
    map.initMap();

    if (document.body.clientWidth <= 750)
    {
        var isCollapsed = true;
    }
    else
    {
        var isCollapsed = false;
    }

}
