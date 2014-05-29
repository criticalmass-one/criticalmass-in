GlobalPage = function()
{
    this.pageIdentifier = "webapp";

    this.initUserStatus();
    this.initEventListeners();
    this.initPageLayout();
}

GlobalPage.prototype = new AppPage();

GlobalPage.prototype.constructor = GlobalPage;

GlobalPage.prototype.initEventListeners = function()
{
    var this2 = this;

    $('a#logoutButton').click(function()
    {
        this2.logout();
    });

    $('#mainpanel').panel({
        open: function() {
            _paq.push(['trackEvent', 'main_menu', 'open']);
        }
    });
}


GlobalPage.prototype.initUserStatus = function()
{
    if (this.getUserLoginStatus() == true)
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
    var city = CityFactory.getCityFromStorageBySlug(citySlug);

    this.setAppTitle(city.getTitle() + ' — criticalmass.in');
    this.refreshCityTitles(city.getTitle());
}
