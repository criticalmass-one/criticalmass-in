function sendPosition(position)
{
	$.ajax({
		type: 'GET',
		url: '/trackposition',
		data: {
			latitude: position.coords.latitude,
			longitude: position.coords.longitude,
			accuracy: position.coords.accuracy,
			altitude: position.coords.altitude,
			altitudeaccuracy: position.coords.altitudeAccurary,
			speed: position.coords.speed,
			heading: position.coords.heading,
			timestamp: position.coords.timestamp
		},
		cache: false,
		success: function(result) {

		}
	});
}


function preparePositionSending()
{
	$.ajax({
		type: 'GET',
		url: '/settings/getgpsstatus',
		data: {
		},
		cache: false,
		success: function(result) {
			if (result.status == true)
			{
				if (navigator.geolocation)
				{
					navigator.geolocation.getCurrentPosition(sendPosition);
				}
				else
				{
					//alert("Geolocation nicht möglich.");
				}
			}
		}
	});
}

function refreshInterval()
{
	$.ajax({
		type: 'GET',
		url: '/settings/getgpsinterval',
		data: {
		},
		cache: false,
		success: function(result) {
			var timer = setInterval(function()
			{
				clearInterval(timer);

				if (result.interval > 0)
				{
					refreshInterval();
					preparePositionSending();
				}
			}, result.interval);
		}
	});
}

function initializeGeolocation()
{
	refreshInterval();
}

google.maps.event.addDomListener(window, 'load', initializeGeolocation);
