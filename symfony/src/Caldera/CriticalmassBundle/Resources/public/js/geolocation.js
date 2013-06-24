function success(position)
{
	window.location = "http://localhost/criticalmass/symfony/web/app_dev.php/selectcity/" + position.coords.latitude + "/" + position.coords.longitude;
}

function error(message)
{
	//alert("Geolocation fehlgeschlagen: " + message);
}

function getLocation()
{
	if (navigator.geolocation)
	{
		navigator.geolocation.getCurrentPosition(success, error);
	}
	else
	{
		//alert("Geolocation nicht möglich.");
	}
}

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

window.onload = setInterval(function()
{
	navigator.geolocation.getCurrentPosition(sendPosition, error);
}, 5000);