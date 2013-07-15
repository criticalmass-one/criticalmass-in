function initialize()
{
  $('#notify_ride').click(function()
  {
    alert('foo');
  });

  $('#notify_time').click(function()
  {
    alert('bar');
  });

  $('#notify_location').click(function()
  {
    alert('baz');
  });
}

google.maps.event.addDomListener(window, 'load', initialize);