function sidebarLocationSuccess(position)
{
	$('#rightpanel').load('/loadcities/', { 'latitude': position.coords.latitude, 'longitude': position.coords.longitude }, function()
	{
		$('#rightpanel').trigger('create');
	});
}

function sidebarLocationNoPosition()
{
	$('#rightpanel').load('/loadcities/', function()
	{
		$('#rightpanel').trigger('create');
	});
}

$(window).load(function()
{
	$('#rightpanel').on('panelbeforeopen', function(event, ui)
	{
		if (navigator.geolocation)
		{
			navigator.geolocation.getCurrentPosition(sidebarLocationSuccess, sidebarLocationNoPosition);
		}
		else
		{
			sidebarLocationNoPosition();
		}
	});
});
