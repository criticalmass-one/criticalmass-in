$(window).load(function()
{
	$('#rightpanel').on('panelbeforeopen', function(event, ui)
	{
		if (navigator.geolocation)
		{
			navigator.geolocation.getCurrentPosition(function(position)
			{
				$('#rightpanel').load('http://localhost/criticalmass/symfony/web/app_dev.php/loadcities/', { latitude: position.coords.latitude, longitude: position.coords.longitude }, function()
				{
					$('#rightpanel').trigger('create');
				});
			});
		}

		
	});
});