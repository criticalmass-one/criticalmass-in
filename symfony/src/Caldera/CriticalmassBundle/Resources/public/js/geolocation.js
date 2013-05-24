function success(position)
{
	window.location = "http://localhost/criticalmass/symfony/web/app_dev.php/selectcity/" + position.coords.latitude + "/" + position.coords.longitude;
}

function error(message)
{
	alert("Geolocation fehlgeschlagen: " + message);
}

function getLocation()
{
	if (navigator.geolocation)
	{
		navigator.geolocation.getCurrentPosition(success, error);
	}
	else
	{
		alert("Geolocation nicht möglich.");
	}
}