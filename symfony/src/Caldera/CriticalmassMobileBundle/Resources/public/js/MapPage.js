MapPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;

    this.initEventListeners();
    this.initMenuUserStatus();
    this.initMap();
}

MapPage.prototype = new AppPage();

MapPage.prototype.constructor = MapPage;

MapPage.prototype.map = null;

MapPage.prototype.initMap = function()
{
    if (this.map == null)
    {
        this.map = new Map('map', CityFactory.getCityBySlug('hamburg'), this);
    }
    else
    {
        throw 'Map is already initialized';
    }
}