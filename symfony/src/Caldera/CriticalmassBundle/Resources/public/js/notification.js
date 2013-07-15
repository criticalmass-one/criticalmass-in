function processNotification(type)
{
  /*
  $.ajax({
    url: '/admin/ride/44/sendnotifications/' + type,
    success: refreshMarkers2
  });*/
}

function initialize()
{
  $('#notify_ride').click(function()
  {
    processNotification('ride');
  });

  $('#notify_time').click(function()
  {
    processNotification('time');
  });

  $('#notify_location').click(function()
  {
    processNotification('location');
  });
}

google.maps.event.addDomListener(window, 'load', initialize);