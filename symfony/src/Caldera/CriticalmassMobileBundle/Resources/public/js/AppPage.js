AppPage = function(pageIdentifier)
{
    if (pageIdentifier && $('#' + pageIdentifier).length)
    {
        this.pageIdentifier = pageIdentifier;
    }
    else if (pageIdentifier)
    {
        alert('PageIdentifier #' + pageIdentifier + ' nicht gefunden!');
    }
};

AppPage.prototype.positionSender = null;

AppPage.prototype.pageIdentifier = null;

AppPage.prototype.setCitySlug = function(newCitySlug)
{
    citySlug = newCitySlug;
}

AppPage.prototype.getCitySlug = function()
{
    return citySlug;
}

AppPage.prototype.getUserLoginStatus = function()
{
    return localStorage.userLoginStatus == 'true';
}

AppPage.prototype.logout = function()
{
    $.ajax({
        type : 'GET',
        context : this,
        url : UrlFactory.getUrlPrefix() + 'logout',
        success : function(data)
        {
            this.switchToLoggedOutMode();
            _paq.push(['trackEvent', 'userstatus', 'logout']);
        }
    });
}

AppPage.prototype.switchToLoggedInMode = function(data)
{
    localStorage.userLoginStatus = true;
    localStorage.userName = data.username;

    var notificationLayer = new NotificationLayer("Hej " + data.username + ", willkommen zurück!");
    this.showNotificationLayer(notificationLayer);

    this.toggleMenuItems();

    $(":mobile-pagecontainer").pagecontainer("change", "#startPage");
}

AppPage.prototype.switchToLoggedOutMode = function()
{
    localStorage.userLoginStatus = false;
    localStorage.userName = null;

    var notificationLayer = new NotificationLayer("Du hast dich gerade abgemeldet. Bis zum nächsten Mal!");
    this.showNotificationLayer(notificationLayer);

    this.toggleMenuItems();
}

AppPage.prototype.toggleMenuItems = function()
{
    $('#profileButton').toggle();
    $('#loginButton').toggle();
    $('#logoutButton').toggle();
    $('#registerButton').toggle();
}

AppPage.prototype.switchCityBySlug = function(newCitySlug)
{
    var newCity = CityFactory.getCityFromStorageBySlug(newCitySlug);

    this.setAppTitle(newCity.getTitle() + ' — criticalmass.in');
    this.refreshCityTitles(newCity.getTitle());
    this.setCitySlug(newCitySlug);

    _paq.push(['trackEvent', 'switch_city', newCitySlug]);
}

AppPage.prototype.setAppTitle = function(newTitle)
{
    $('title').html(newTitle);
}

AppPage.prototype.refreshCityTitles = function(newTitle)
{
    $('.city-full-title').html(newTitle);
}

AppPage.prototype.showNotificationLayer = function(notificationLayer)
{
    if ($('#notificationLayer').length == 0)
    {
        $('#' + this.pageIdentifier + ' section[data-role="content"]').prepend('<div id="notificationLayer" class="notification" data-icon="navigation">' + notificationLayer.getNotificationMessage() + '</div>');

        var this2 = this;

        $('#notificationLayer').click(function()
        {
            this2.flushNotification();
        });
    }
}

AppPage.prototype.flushNotification = function()
{
    $('div#notificationLayer').slideUp(250, function() {
        this.remove();
    });
}