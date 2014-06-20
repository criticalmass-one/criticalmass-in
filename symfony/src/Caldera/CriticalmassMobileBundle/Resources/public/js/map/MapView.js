MapView = function(map)
{
    this.map = map;
};

MapView.prototype.map = null;

MapView.prototype.initEventListeners = function()
{
    var this2 = this;

    this.map.map.on('dragend', function()
    {
        this2.refreshOverridingMapPosition();
    });

    this.map.map.on('zoomend', function()
    {
        this2.refreshOverridingMapPosition();
    });
};

MapView.prototype.getOverridingMapPosition = function()
{
    var address = new String(window.location);
    var addressArray = address.split('/');
    var tail = addressArray.pop();

    if (tail.indexOf('@') > 0)
    {
        var positionString = tail.split('@').pop();
        var positionArray = positionString.split(',');

        return { latitude: positionArray.shift(), longitude: positionArray.shift(), zoomFactor: positionArray.shift() };
    }

    return null;
};

MapView.prototype.hasOverridingMapPosition = function()
{
    var address = new String(window.location);

    return address.indexOf('@') > 0;
};

MapView.prototype.refreshOverridingMapPosition = function()
{
    var address = new String(window.location);
    var addressArray = address.split('/');
    var tail = addressArray.pop();

    tail = tail.slice(tail.indexOf('@'), tail.indexOf('#'));

    var positionString = this.map.map.getCenter().lat + "," + this.map.map.getCenter().lng + "," + this.map.map.getZoom();
    tail = positionString + tail;

    addressArray.push(tail);

    address = addressArray.join();

    window.location = '#mapPage@' + positionString;
};