GlobalPage = function()
{
    this.pageIdentifier = "webapp";
}

GlobalPage.prototype = new AppPage();

GlobalPage.prototype.constructor = GlobalPage;

GlobalPage.prototype.initPage = function()
{
    $(function() { $( "body>[data-role='panel']" ).panel().enhanceWithin(); });

    this.initUserStatus();
    this.initEventListeners();
    this.initPageLayout();
}

GlobalPage.prototype.initEventListeners = function()
{
    var this2 = this;

    $('a#logoutButton').click(function()
    {
        this2.logout();
    });

    $('#mainpanel').panel({
        open: function() {
            _paq.push(['trackEvent', 'mainMenu', 'open']);
        }
    });
}


GlobalPage.prototype.initUserStatus = function()
{
    if (this.isUserLoggedIn() == true)
    {
        $('.show-loggedin-only').show();
        $('.show-loggedout-only').hide();
    }
    else
    {
        $('.show-loggedout-only').show();
        $('.show-loggedin-only').hide();
    }
}

GlobalPage.prototype.initPageLayout = function()
{
    var city = CityFactory.getCityFromStorageBySlug(this.getCitySlug());

    this.setAppTitle(city.getTitle() + ' — criticalmass.in');
    this.refreshCityTitles(city);

    this.storeCurrentCity();
}
