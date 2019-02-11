define(['jquery', 'jquery.dataTables', 'Map', 'CityMarker'], function ($) {
    var CityListPage = function (context) {
        this.cityListTableSelector = context;

        this._init();
    };

    CityListPage.prototype._init = function () {
        let table = $(this.cityListTableSelector).DataTable({
            'paging': false
        });

        let that = this;

        $('button.show-more').on('click', function () {
            var $tr = $(this).closest('tr');
            var row = table.row($tr);

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
        let citySlug = $tr.data('city-slug');
        let url = Routing.generate('caldera_criticalmass_rest_city_show', { citySlug: citySlug});

        $.ajax(url, {
            success: function(cityData) {
                let html = '<div class="row"><div class="col-md-6"><div style="height: 150px;" id="map-' + citySlug + '"></div></div><div class="col-md-6"><h4>' + cityData.title + '</h4></div></div>';

                row.child(html).show();
                $tr.addClass('shown');

                let map = new Map('map-' + citySlug);
                let mapCenter = [cityData.latitude, cityData.longitude];

                map.setView(mapCenter, 12);
                let cityMarker = new CityMarker(mapCenter);
                cityMarker.addTo(map);
            }
        });
    };

    return CityListPage;
});