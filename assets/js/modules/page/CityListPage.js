define(['jquery', 'dateformat', 'jquery.dataTables', 'Map', 'CityMarker'], function ($, dateFormat) {
    var CityListPage = function (context) {
        this.cityListTableSelector = context;

        this._init();
    };

    CityListPage.prototype._init = function () {
        const table = $(this.cityListTableSelector).DataTable({
            'paging': false
        });

        const that = this;

        $('button.show-more').on('click', function () {
            const $tr = $(this).closest('tr');
            const row = table.row($tr);

            if (row.child.isShown()) {
                row.child.hide();
                $tr.removeClass('shown');
                $(this).find('i').removeClass('fa-minus-circle').addClass('fa-plus-circle');
            }
            else {
                that._buildChildRow($tr, row);
                $(this).find('i').removeClass('fa-plus-circle').addClass('fa-minus-circle');
            }
        } );
    };

    CityListPage.prototype._buildChildRow = function($tr, row) {
        const citySlug = $tr.data('city-slug');

        function loadCityData() {
            const url = Routing.generate('caldera_criticalmass_rest_city_show', { citySlug: citySlug});

            return $.ajax({
                url: url,
                dataType: 'json',
            });
        }

        function loadRideData() {
            const url = Routing.generate('caldera_criticalmass_rest_ride_show_current', { citySlug: citySlug});

            return $.ajax({
                url: url,
                dataType: 'json',
            });
        }

        $.when(loadCityData(), loadRideData()).done(function(cityResponse, rideResponse) {
            const cityData = cityResponse[0];
            const rideData = rideResponse[0];

            console.log(cityData, rideData);

            let html = '<div class="row">';
            html += '<div class="col-md-6">';
            html += '<div style="height: 150px;" id="map-' + citySlug + '"></div>';
            html += '</div>';
            html += '<div class="col-md-6">';
            html += '<h4>' + cityData.title + '</h4>';

            if (cityData.punchline) {
                html += '<p class="lead">' + cityData.punchline + '</p>';
            }

            if (rideData) {
                html += '<p><strong>Nächste Tour</strong></p>';

                html += '<p>' + dateFormat(rideData.dateTime * 1000, 'dd.mm.yyyy HH:MM') + '&nbsp;Uhr';
                html += '<br />' + rideData.location + '</p>';
            }

            html += '</div>';
            html += '</div>';

            row.child(html).show();
            $tr.addClass('shown');

            const map = new Map('map-' + citySlug);
            const mapCenter = [cityData.latitude, cityData.longitude];

            map.setView(mapCenter, 12);

            const cityMarker = new CityMarker(mapCenter);
            cityMarker.addTo(map);
        });
    };

    return CityListPage;
});