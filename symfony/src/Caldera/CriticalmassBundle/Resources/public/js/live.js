var markerArray = [];

function setMapOptions(result)
{
	var mapOptions = {
		zoom: result.zoom,
		center: new google.maps.LatLng(result.mapcenter.latitude, result.mapcenter.longitude),
		mapTypeId: google.maps.MapTypeId.TERRAIN,
		disableDefaultUI: true
	}

	var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

	for (var pos in result.positions)
	{
		var circleOptions = {
			strokeColor: result.positions[pos].strokeColor,
			strokeOpacity: result.positions[pos].strokeOpacity,
			strokeWeight: result.positions[pos].strokeWeight,
			fillColor: result.positions[pos].fillColor,
			fillOpacity: result.positions[pos].fillOpacity,
			map: map,
			center: new google.maps.LatLng(result.positions[pos].latitude, result.positions[pos].longitude),
			radius: result.positions[pos].radius
		};

		cityCircle = new google.maps.Circle(circleOptions);
		markerArray.push(cityCircle);
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

function startMapInitialization()
{
	initializeMap();

	var timer = setInterval(initializeMap, 5000);
}

google.maps.event.addDomListener(window, 'load', startMapInitialization);
