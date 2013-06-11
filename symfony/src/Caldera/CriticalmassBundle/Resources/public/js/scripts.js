$(window).load(function()
{
	$('#rightpanel').on('panelbeforeopen', function(event, ui)
	{
		alert("Panel wird gleich geöffnet.");
	});
});