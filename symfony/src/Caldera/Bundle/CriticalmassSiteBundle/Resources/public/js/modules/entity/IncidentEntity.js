define(['leaflet', 'BaseEntity', 'leaflet-polyline', 'leaflet-extramarkers', 'Modal'], function() {
    IncidentEntity = function(id, title, description, geometryType, incidentType, polyline, expires, visibleFrom, visibleTo) {
        this._id = id;
        this._title = title;
        this._description = description;
        this._geometryType = geometryType;
        this._incidentType = incidentType;
        this._polyline = polyline;
        this._expires = expires;
        this._visibleFrom = visibleFrom;
        this._visibleTo = visibleTo;
    };

    IncidentEntity.prototype = new BaseEntity();
    IncidentEntity.prototype.constructor = IncidentEntity;

    IncidentEntity.prototype._id = null;
    IncidentEntity.prototype._title = null;
    IncidentEntity.prototype._description = null;
    IncidentEntity.prototype._geometryType = null;
    IncidentEntity.prototype._incidentType = null;
    IncidentEntity.prototype._polyline = null;
    IncidentEntity.prototype._expires = null;
    IncidentEntity.prototype._visibleFrom = null;
    IncidentEntity.prototype._visibleTo = null;
    IncidentEntity.prototype._layer = null;

    IncidentEntity.prototype.addToLayer = function(markerLayer) {
        var latLngList = L.PolylineUtil.decode(this._polyline);

        var polyOptions = { color: 'red' };

        if (this._geometryType == 'polygon') {
            this._layer = new L.polygon(latLngList, polyOptions);
        }

        if (this._geometryType == 'polyline') {
            this._layer = new L.polyline(latLngList, polyOptions)
        }

        if (this._geometryType == 'marker') {
            var icon = markerIcon = L.ExtraMarkers.icon({
                icon: 'fa-bomb',
                markerColor: 'red',
                shape: 'round',
                prefix: 'fa'
            });

            this._layer = new L.marker(latLngList[0], { icon: icon });
        }

        if (this._layer) {
            markerLayer.addLayer(this._layer);
            this._initPopup();
        }
    };

    IncidentEntity.prototype._initPopup = function() {
        this._modal = new Modal();
        this._modal.setSize('md');

        this._modal.setTitle(this._title);
        this._modal.setBody(this._description);

        var that = this;

        this._layer.on('click', function() {
            that.openPopup();
        });
    };

    IncidentEntity.prototype.openPopup = function() {
        this._modal.show();
    };

    IncidentEntity.prototype.getBounds = function() {
        return this._layer.getBounds();
    };

    return IncidentEntity;
});
