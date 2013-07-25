function initialize() {
	$.ajax({
		type: 'GET',
		url: '/mapapi/getridelocation/' + citySlugString,
		data: {
		},
		success: function(result) {
			var latlng = new google.maps.LatLng(result.latitude, result.longitude);

			var mapOptions = {
				zoom: 12,
				center: latlng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}

			var map = new google.maps.Map(document.getElementById('information-map'), mapOptions);

			var marker = new google.maps.Marker({
				map: map,
				position: latlng
		  });
		}
	});
}

google.maps.event.addDomListener(window, 'load', initialize);