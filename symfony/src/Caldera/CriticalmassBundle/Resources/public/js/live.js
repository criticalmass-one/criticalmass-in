var markersArray = [];
var map;

function addMarker(options)
{
	var circleOptions = {
		strokeColor: options.strokeColor,
		strokeOpacity: options.strokeOpacity,
		strokeWeight: options.strokeWeight,
		fillColor: options.fillColor,
		fillOpacity: options.fillOpacity,
		map: map,
		center: new google.maps.LatLng(options.latitude, options.longitude),
		radius: options.radius
	};

	circleMarker = new google.maps.Circle(circleOptions);
	markersArray[options.id] = circleMarker;
}

function clearMarkers()
{
	if (markersArray)
	{
		for (i in markersArray)
		{
			markersArray[i].setMap(null);
		}
	}
}

function refreshMarkers()
{
	//clearMarkers();
	startLoadingMarkers();
}

function existsMarker(options)
{
	var found = false;

	for (i in markersArray)
	{
		if (i == options.id)
		{
			found = true;
			break;
		}
	}

	return found;
}

function startLoadingMarkers()
{
	$.ajax({
		type: 'GET',
		url: '/mapapi/mapdata',
		data: {
		},
		cache: false,
		success: proceedLoadingMarkers
	});
}

function proceedLoadingMarkers(result)
{
	for (var pos in result.positions)
	{
		if (!existsMarker(result.positions[pos]))
		{
			addMarker(result.positions[pos]);
		}
	}
}

function setMapOptions(result)
{
	var mapOptions = {
		zoom: result.zoom,
		center: new google.maps.LatLng(result.mapcenter.latitude, result.mapcenter.longitude),
		mapTypeId: google.maps.MapTypeId.TERRAIN,
		disableDefaultUI: true
	}

	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

	proceedLoadingMarkers(result);
}

function initializeLivePage()
{
	if ($('#map-canvas').length > 0)
	{
		$.ajax({
			type: 'GET',
			url: '/mapapi/mapdata',
			data: {
			},
			cache: false,
			success: setMapOptions
		});
	}

	$( "#slider-gps-interval" ).on( "slidestop", function( event, ui ) {
		$.ajax({
			type: 'GET',
			url: '/settings/gpsinterval',
			data: {
				'interval': event.target.value
			},
			cache: false
		});
	} );

	$( "#flip-gps-sender" ).on( "slidestop", function( event, ui ) {
		$.ajax({
			type: 'GET',
			url: '/settings/gpsstatus',
			data: {
				'status': $("select#flip-gps-sender")[0].selectedIndex
			},
			cache: false
		});
	} );
}

function startInitialization()
{
	initializeLivePage();

	var timer = setInterval(refreshMarkers, 5000);
}

google.maps.event.addDomListener(window, 'load', startInitialization);
