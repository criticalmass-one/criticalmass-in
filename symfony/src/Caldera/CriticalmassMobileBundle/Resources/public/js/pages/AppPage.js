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

AppPage.prototype.hasCityslug = function()
{
    return sessionStorage.citySlug && sessionStorage.citySlug != null;
}

AppPage.prototype.setCitySlug = function(newCitySlug)
{
    sessionStorage.citySlug = newCitySlug;
}

AppPage.prototype.getCitySlug = function()
{
    return sessionStorage.citySlug;
}

AppPage.prototype.isUserLoggedIn = function()
{
    return sessionStorage.userLoginStatus == 'true';
}

AppPage.prototype.logout = function()
{
    $.ajax({
        type : 'GET',
        context : this,
        url : Url.getUrlPrefix() + 'logout',
        success : function(data)
        {
            this.switchToLoggedOutMode();
            _paq.push(['trackEvent', 'logout', 'success']);
        }
    });
}

AppPage.prototype.switchToLoggedInMode = function(username)
{
    sessionStorage.userLoginStatus = true;
    sessionStorage.userName = username;

    this.storeCurrentCity();
    this.toggleMenuItems();
}

AppPage.prototype.switchToLoggedOutMode = function()
{
    sessionStorage.userLoginStatus = false;
    sessionStorage.userName = null;

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
    this.refreshCityTitles(newCity);
    this.setCitySlug(newCitySlug);
    this.storeCurrentCity(newCitySlug);

    $('#glympseInviteEmailAddress').html(newCitySlug + '@criticalmass.in');

    _paq.push(['trackEvent', 'switchCity', newCitySlug]);
}

AppPage.prototype.storeCurrentCity = function()
{
    $.ajax({
        type : 'GET',
        context : this,
        url : Url.getApiPrefix() + 'user/switchcity/' + this.getCitySlug(),
        success : function(data)
        {
        }
    });
}

AppPage.prototype.setAppTitle = function(newTitle)
{
    $('title').html(newTitle);
}

AppPage.prototype.refreshCityTitles = function(city)
{
    $('.city-full-title').html(city.getTitle());
    $('.city-short-title').html(city.getCity());
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