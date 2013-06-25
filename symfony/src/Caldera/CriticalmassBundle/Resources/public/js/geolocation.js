function sendPosition(position)
{
	$.ajax({
		type: 'GET',
		url: '/criticalmass/symfony/web/app_dev.php/trackposition',
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
		url: '/criticalmass/symfony/web/app_dev.php/settings/getgpsstatus',
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
		url: '/criticalmass/symfony/web/app_dev.php/settings/getgpsinterval',
		data: {
		},
		cache: false,
		success: function(result) {
			var timer = setInterval(function()
			{
				clearInterval(timer);
				refreshInterval();
				preparePositionSending();
			}, result.interval);
		}
	});
}

function initialize()
{
	refreshInterval();
}

google.maps.event.addDomListener(window, 'load', initialize);