var map, sidebar;

function getViewport() {
  if (sidebar.isVisible()) {
    map.setActiveArea({
      position: "absolute",
      top: "0px",
      left: $(".leaflet-sidebar").css("width"),
      right: "0px",
      height: $("#map").css("height")
    });
  } else {
    map.setActiveArea({
      position: "absolute",
      top: "0px",
      left: "0px",
      right: "0px",
      height: $("#map").css("height")
    });
  }
}

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

    resetCityInfo();

    var imageFilename = Url.getUrlPrefix() + 'images/city/' + slug + '.jpg';

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

    $('#cityModalTitle').html(city.getTitle());
    $('#cityModalTabInfoJumbotronTitle').html(city.getTitle());
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

    var ride = RideFactory.getRideFromStorageBySlug(slug);

    if (ride)
    {
        $('#cityModalTabNextRideKnown').show();
        $('#cityModalTabNextRideUnknown').hide();

        var imageFilename = Url.getUrlPrefix() + 'images/ride/' + slug + '/' + ride.getId() + '.jpg';

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
        }

        $('#cityModalTabNextRideJumbotronTitle').html(ride.getTitle());
        $('#cityModalTabNextRideTitle').html(ride.getTitle());
        $('#cityModalTabNextRideDescription').html(ride.getDescription());

        if (ride.getLocation())
        {
            $('#cityModalTabNextRideLocation').html(ride.getLocation());
        }
        else
        {
            $('#cityModalTabNextRideLocation').html('Der Treffpunkt ist noch nicht bekannt :(');
        }

        $('#cityModalTabNextRideDate').html(ride.getFormattedDate());
        $('#cityModalTabNextRideTime').html(ride.getFormattedTime());
        $('#cityModalTabNextRideGlympse').html(slug + '@criticalmass.in');

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

        $('#cityModalTabNextRideSocialMedia').html(html);
    }
    else
    {
        $('#cityModalTabNextRideKnown').hide();
        $('#cityModalTabNextRideUnknown').show();
    }

    $('#cityInfoModal').modal('show');

    map.setView([city.getLatitude(), city.getLongitude()], 15);
}

if (document.body.clientWidth <= 767) {
    var isCollapsed = true;
} else {
    var isCollapsed = false;
    /*sidebar.show();*/
}

function initApp()
{
    var tileLayerObjects = TileLayerFactory.getTileLayers();
    var tileLayers = new Array();
    var standardTileLayer = null;

    for (var index in tileLayerObjects)
    {
        var tileLayerObject = tileLayerObjects[index];
        var tileLayer = L.tileLayer(tileLayerObject.getAddress(), {foo: 'bar'});

        tileLayers[tileLayerObject.getTitle()] = tileLayer;

        if (tileLayerObject.getStandard())
        {
            standardTileLayer = tileLayer;
        }
    }

    var cities = CityFactory.getAllCities();

    for (var index in cities)
    {
        var city = cities[index];
        var html = '<tr class="cityRow" data-cityslug="' + city.getCitySlug() + '" style="cursor: pointer;"><td class="cityName">' + city.getCity() + '<i class="fa fa-chevron-right pull-right"></i></td></tr>';

        $('#cityList').append(html);
    }

    $('.cityRow').on('click', function()
    {
        showCityInfo($(this).data('cityslug'));
    });

    /*map = L.map('map', { zoomControl: false, attributionControl: false });

    var initMapView = new InitMapView(map, citySlug);
    initMapView.initView();*/

    //map.setView([53.5554952, 9.9436765], 13);

    map = new Map('map');
    map.initMap();

    standardTileLayer.addTo(map.map);

    sidebar = L.control.sidebar("sidebar", {
        closeButton: true,
        position: "left"
    }).on("shown", function () {
        getViewport();
    }).on("hidden", function () {
        getViewport();
    }).addTo(map.map);

    if (document.body.clientWidth <= 767)
    {
        var isCollapsed = true;
    }
    else
    {
        var isCollapsed = false;
        sidebar.show();
    }


    var zoomControl = L.control.zoom({
        position: "topright"
    }).addTo(map.map);



    var heatmapGroup = L.layerGroup([L.tileLayer("https://www.criticalmass.cm/images/heatmap/ae5e6d7e21c2936051a06a0f2f40661e/{z}/{x}/{y}.png", {
        maxZoom: 18
    })]);


    getViewport();

    var layerControl = L.control.groupedLayers(tileLayers, {
        "Critical Mass": {
            "Städte": mapCities.layerGroup,
            "Teilnehmer": mapPositions.layerGroup,
            "Heatmaps": heatmapGroup
        }
    }, {
        collapsed: false
    });

    layerControl.addTo(map.map);
}