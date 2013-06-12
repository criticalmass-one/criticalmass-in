$(window).load(function()
{
	$('#rightpanel').on('panelbeforeopen', function(event, ui)
	{
		$('#rightpanel').load('http://localhost/criticalmass/symfony/web/app_dev.php/loadcities/55.0/33.0');
	});
});