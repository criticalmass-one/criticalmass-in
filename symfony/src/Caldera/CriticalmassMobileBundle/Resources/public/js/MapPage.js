MapPage = function(pageIdentifier)
{
    AppPage.call(this, pageIdentifier);

    this.initMap();
}

MapPage.prototype = new AppPage();

MapPage.prototype.constructor = MapPage;

MapPage.prototype.map = null;

MapPage.prototype.initMap = function()
{
    if (this.map == null)
    {
        this.map = new Map('map', new City('hamburg'));
    }
    else
    {
        throw 'Map is already initialized';
    }
}