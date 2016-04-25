define(['leaflet', 'BaseEntity', 'leaflet-polyline'], function() {
    IncidentEntity = function(title, description, geometryType, incidentType, polyline, expires, visibleFrom, visibleTo) {
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

        if (this._geometryType == 'polygon') {
            this._layer = new L.polygon(latLngList);
        }

        if (this._geometryType == 'polyline') {
            var latLngList = L.PolylineUtil.decode(this._polyline);

            this._layer = new L.polyline(latLngList)
        }

        if (this._geometryType == 'marker') {
            var latLng = L.PolylineUtil.decode(this._polyline);

            this._layer = new L.marker(latLngList[0]);
        }

        markerLayer.addLayer(this._layer);
    };

    return IncidentEntity;
});
