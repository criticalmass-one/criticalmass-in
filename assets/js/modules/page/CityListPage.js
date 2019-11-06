define(['jquery', 'dateformat', 'jquery.dataTables', 'Map', 'CityMarker', 'LocationMarker'], function ($, dateFormat) {
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

        function renderDetailsRow(cityResponse, rideResponse) {
            const cityData = cityResponse[0];
            const rideData = rideResponse !== 'error' ? rideResponse[0] : null;

            let html = '<div class="row">';
            html += '<div class="col-md-6">';
            html += '<div style="height: 150px;" id="map-' + citySlug + '"></div>';
            html += '</div>';
            html += '<div class="col-md-6">';
            html += '<h4>' + cityData.title + '</h4>';

            if (cityData.punchline) {
                html += '<p class="lead">' + cityData.punchline + '</p>';
            }

            let mapCenter = [cityData.latitude, cityData.longitude];
            let marker = new CityMarker(mapCenter);

            if (rideData !== null && rideData.id) {
                if (rideData.latitude && rideData.longitude) {
                    mapCenter = [rideData.latitude, rideData.longitude];
                    marker = new LocationMarker(mapCenter);
                }

                html += '<p><strong>Nächste Tour:</strong></p>';

                html += '<p>' + dateFormat(rideData.dateTime * 1000, 'dd.mm.yyyy HH:MM') + '&nbsp;Uhr';
                html += '<br />' + rideData.location + '</p>';

                const rideDate = dateFormat(rideData.dateTime * 1000, 'yyyy-mm-dd');
                const rideUrl = Routing.generate('caldera_criticalmass_ride_show', { citySlug: citySlug, rideDate: rideDate});

                html += '<p><a href="' + rideUrl + '" class="btn btn-primary"><i class="far fa-bicycle"></i> Mehr erfahren</a></p>';
            } else {
                if (cityData.description) {
                    html += '<p>' + cityData.description + '</p>';
                }

                const cityUrl = Routing.generate('caldera_criticalmass_city_show', { citySlug: citySlug});

                html += '<p><a href="' + cityUrl + '" class="btn btn-primary"><i class="far fa-university"></i> Mehr erfahren</a></p>';
            }

            html += '</div>';
            html += '</div>';

            row.child(html).show();
            $tr.addClass('shown');

            const map = new Map('map-' + citySlug);
            map.setView(mapCenter, 12);
            marker.addTo(map);
        }

        $.when(loadCityData(), loadRideData()).done(renderDetailsRow);
    };

    return CityListPage;
});