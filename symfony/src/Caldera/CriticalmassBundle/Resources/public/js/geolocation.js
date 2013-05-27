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

function sendPosition(position)
{
  $.ajax({
      type: 'POST',
      url: 'http://localhost/criticalmass/symfony/web/app_dev.php/trackposition',
      data: {
        latitude: position.coords.latitude,
        longitude: position.coords.longitude,
        altitude: position.coords.altitude
      },
      cache: false,
      success: function(result) {
          if (result == "true"){
              alert("true");
          }else{
              alert("false");
          }
      }
  });
}
window.onload = setInterval(function()
{
	navigator.geolocation.getCurrentPosition(sendPosition, error);
}, 5000);