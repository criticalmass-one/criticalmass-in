var map;
var marker;

function initialize() {
	$.ajax({
		type: 'GET',
		url: '/mapapi/getridelocation/hamburg',
		data: {
		},
		success: function(result) {
		  var latlng = new google.maps.LatLng(result.latitude, result.longitude);
		  var mapOptions = {
				zoom: 12,
				center: latlng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
		  }
		  map = new google.maps.Map(document.getElementById('information-map'), mapOptions);
			
		}
	});
}

google.maps.event.addDomListener(window, 'load', initialize);