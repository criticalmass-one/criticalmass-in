$(window).load(function()
{
	$('#rightpanel').on('panelbeforeopen', function(event, ui)
	{
		if (navigator.geolocation)
		{
			navigator.geolocation.getCurrentPosition(function(position)
			{
				$('#rightpanel').load('/loadcities/', { 'latitude': position.coords.latitude, 'longitude': position.coords.longitude }, function()
				{
					$('#rightpanel').trigger('create');
				});
			});
		}
		else
		{
			$('#rightpanel').load('/loadcities/', function()
			{
				$('#rightpanel').trigger('create');
			});
		}
	});
});
