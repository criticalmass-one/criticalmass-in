var markerArray = [];
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
	markerArray.push(circleMarker);
}

function clearMarker()
{
	if (markerArray)
	{
		for (i in markerArray)
		{
			markerArray[i].setMap(null);
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

	for (var pos in result.positions)
	{
		addMarker(result.positions[pos]);
	}
}

function initializeMap()
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
	initializeMap();

	var timer = setInterval(initializeMap, 5000);
}

google.maps.event.addDomListener(window, 'load', startInitialization);
